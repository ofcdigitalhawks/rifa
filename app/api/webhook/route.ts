import { type NextRequest, NextResponse } from "next/server"
import { Database } from "@/lib/database"

export async function POST(request: NextRequest) {
  try {
    // Capturar o payload JSON da Nivopay
    const webhookData = await request.json()
    
    console.log("📬 Webhook recebido:", JSON.stringify(webhookData, null, 2))
    
    // Extrair dados do webhook
    const status = webhookData.status ? webhookData.status.toUpperCase() : ''
    const paymentId = webhookData.paymentId || webhookData.id || ''
    const totalValue = webhookData.totalValue || webhookData.amount || 0
    const customer = webhookData.customer || {}
    const paymentMethod = webhookData.paymentMethod || 'PIX'
    
    if (!paymentId) {
      console.warn("⚠️ Webhook sem paymentId:", webhookData)
      return NextResponse.json({
        success: false,
        error: 'paymentId não fornecido'
      })
    }

    // Salvar no banco interno
    const db = Database
    
    if (status === 'APPROVED' && paymentId) {
      console.log("✅ Pagamento APROVADO - ID:", paymentId, "- Valor:", totalValue)
      
      // Atualizar status no banco interno
      await db.updateStatus(paymentId, status)
      await db.insert({
        payment_id: paymentId,
        status: status,
        amount: totalValue,
        customer_name: customer.name || '',
        customer_email: customer.email || '',
        customer_cpf: customer.cpf || '',
        customer_phone: customer.phone || '',
        payment_method: paymentMethod,
        action: 'WEBHOOK_APPROVED'
      })
      
      return NextResponse.json({
        success: true,
        message: 'Webhook processado com sucesso',
        paymentId: paymentId,
        status: status
      })
    } else {
      console.log("📝 Webhook recebido - Status:", status, "- ID:", paymentId)
      
      // Salvar outros status também
      await db.insert({
        payment_id: paymentId,
        status: status,
        amount: totalValue,
        customer_name: customer.name || '',
        customer_email: customer.email || '',
        customer_cpf: customer.cpf || '',
        customer_phone: customer.phone || '',
        payment_method: paymentMethod,
        action: `WEBHOOK_${status}`
      })
      
      return NextResponse.json({
        success: true,
        message: 'Webhook recebido',
        status: status
      })
    }
    
  } catch (error) {
    console.error("❌ Erro ao processar webhook:", error)
    
    return NextResponse.json({
      success: false,
      error: error instanceof Error ? error.message : 'Erro desconhecido'
    })
  }
} 