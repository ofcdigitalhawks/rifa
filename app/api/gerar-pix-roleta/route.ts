import { type NextRequest, NextResponse } from "next/server"
import { NivopayService } from "@/lib/nivopay"
import { generateCustomerData } from "@/lib/fake-data"
import { Database } from "@/lib/database"

export async function POST(request: NextRequest) {
  try {
    const body = await request.json()
    const { name, phone, amount = 5490, prize_name } = body

    console.log("🎰 Gerando PIX para Roleta da Sorte")
    console.log("- Nome:", name)
    console.log("- Telefone:", phone)
    console.log("- Valor:", amount)
    console.log("- Prêmio:", prize_name)

    // Verificar valor mínimo
    if (amount < 500) {
      return NextResponse.json({
        error: true,
        message: "O valor mínimo permitido é R$ 5,00"
      })
    }

    // Gerar dados complementares do cliente (usando 4Devs + fallback local)
    const fakeData = await generateCustomerData(true)
    
    // Combinar dados reais com dados gerados
    const customer = {
      name: name || fakeData.name,
      email: fakeData.email,
      cpf: fakeData.cpf,
      phone: phone?.replace(/\D/g, '') || fakeData.phone
    }

    const checkoutUrl = `https://${request.headers.get('host')}/roleta`
    const referrerUrl = request.headers.get('referer') || ''
    const productTitle = prize_name ? `Roleta da Sorte - Prêmio: ${prize_name}` : 'Roleta da Sorte'

    console.log("🔄 Criando pagamento via Nivopay para Roleta...")

    // Criar pagamento via Nivopay
    const paymentResult = await NivopayService.createPayment(
      amount,
      customer,
      productTitle,
      '',
      checkoutUrl,
      referrerUrl
    )

    // Verificar se houve erro na criação
    if ('error' in paymentResult && paymentResult.error) {
      console.error("❌ Erro na geração do PIX:", paymentResult.message)
      return NextResponse.json({
        error: true,
        message: paymentResult.message || "Erro ao gerar o PIX",
        details: paymentResult.details
      })
    }

    const payment = paymentResult as any

    console.log("✅ PIX gerado com sucesso para Roleta:", payment.id)

    // Salvar no banco interno
    try {
      await Database.insert({
        payment_id: payment.id,
        status: 'PENDING',
        amount: amount,
        customer_name: customer.name,
        customer_email: customer.email,
        customer_cpf: customer.cpf,
        customer_phone: customer.phone,
        pix_code: payment.pixCode,
        action: 'GENERATED_ROLETA'
      })
      console.log("💾 Pagamento da Roleta salvo no banco interno")
    } catch (dbError) {
      console.warn("⚠️ Erro ao salvar no banco:", dbError)
    }

    // Retornar dados formatados para o frontend
    return NextResponse.json({
      success: true,
      pix_code: payment.pixCode,
      pix_qr_code: payment.pixCode,
      transaction_id: payment.id,
      amount: payment.amount || amount,
      customer: {
        name: customer.name,
        email: customer.email,
        cpf: customer.cpf,
        phone: customer.phone
      }
    })

  } catch (error) {
    console.error("❌ Erro geral na geração do PIX da Roleta:", error)
    return NextResponse.json({
      error: true,
      message: "Erro interno ao gerar PIX da Roleta",
      debug: error instanceof Error ? error.message : String(error)
    })
  }
}
