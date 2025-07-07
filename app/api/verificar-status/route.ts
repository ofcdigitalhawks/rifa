import { type NextRequest, NextResponse } from "next/server"
import { NivopayService } from "@/lib/nivopay"
import { Database } from "@/lib/database"

export async function GET(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url)
    const hash = searchParams.get("hash")

    if (!hash) {
      console.error("❌ ID da transação não fornecido na requisição")
      return NextResponse.json({
        erro: true,
        mensagem: "ID da transação não fornecido",
      })
    }

    console.log("🔍 Verificando status da transação:", hash)

    // Consultar status diretamente via Nivopay
    const paymentResult = await NivopayService.getPaymentStatus(hash)

    if ('error' in paymentResult && paymentResult.error) {
      console.error("❌ Erro ao consultar status:", paymentResult.message)
      return NextResponse.json({
        erro: true,
        mensagem: paymentResult.message || "Erro ao verificar status da transação",
        success: false,
        status: 'ERROR',
        debug: paymentResult.details
      })
    }

    const payment = paymentResult as any // Type assertion

    console.log("📊 Status obtido da Nivopay:", payment.status)

    // Atualizar banco interno apenas se status não for PENDING
    if (payment.status !== 'PENDING') {
      try {
        await Database.updateStatus(hash, payment.status)
        await Database.insert({
          payment_id: hash,
          status: payment.status,
          amount: payment.amount || 0,
          action: 'VERIFIED'
        })
        console.log("💾 Status atualizado no banco interno")
      } catch (dbError) {
        console.warn("⚠️ Erro ao atualizar banco:", dbError)
      }
    }

    // Mapear status para o formato esperado pelo frontend
    const paymentStatus = (payment.status === 'APPROVED' || payment.status === 'PAID') ? 'paid' : 'pending'
    
    console.log("📌 Status processado:", payment.status, "→", paymentStatus)

    // Retornar resposta no formato esperado pelo frontend
    return NextResponse.json({
      success: true,
      status: payment.status,
      payment_status: paymentStatus, // Campo principal que o frontend procura
      transaction_id: payment.id || hash,
      amount: payment.amount,
      payment_method: payment.method || 'PIX',
      created_at: payment.createdAt,
      updated_at: payment.updatedAt,
      customer: payment.customer,
      pix_code: payment.pixCode,
      pix_qr_code: payment.pixQrCode,
      expires_at: payment.expires_at,
      paid_at: payment.paid_at,
      // Campos adicionais para compatibilidade
      erro: (payment.status === 'APPROVED' || payment.status === 'PAID') ? false : undefined,
      approved: (payment.status === 'APPROVED' || payment.status === 'PAID')
    })

  } catch (error) {
    console.error("❌ Erro geral na verificação:", error)
    return NextResponse.json({
      erro: true,
      mensagem: "Erro interno ao verificar status da transação",
      success: false,
      status: 'ERROR',
      debug: error instanceof Error ? error.message : String(error)
    })
  }
}
