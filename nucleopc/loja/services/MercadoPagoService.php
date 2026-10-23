<?php
require_once __DIR__ . '/../config/mercadopago.php';

/**
 * MercadoPagoService
 * Encapsula as chamadas HTTP à API do Mercado Pago (Checkout Pro):
 * criação de preferência de pagamento e consulta do status de um pagamento.
 *
 * Documentação oficial: https://www.mercadopago.com.br/developers/pt/docs/checkout-pro/landing
 */
class MercadoPagoService
{
    /**
     * Cria uma preferência de pagamento para um pedido e retorna a
     * resposta decodificada da API (contém "id", "init_point" e
     * "sandbox_init_point").
     *
     * @param array $itens cada item: ['nome' => string, 'quantidade' => int, 'preco' => float]
     */
    public function criarPreferencia(int $pedidoId, array $itens, string $emailComprador): array
    {
        $items = array_map(function ($item) {
            return [
                'title'       => $item['nome'],
                'quantity'    => (int) $item['quantidade'],
                'unit_price'  => (float) $item['preco'],
                'currency_id' => 'BRL',
            ];
        }, $itens);

        $payload = [
            'items'               => $items,
            'payer'               => ['email' => $emailComprador],
            'external_reference'  => (string) $pedidoId,
            'back_urls'           => [
                'success' => MP_BASE_URL . '/index.php?url=pedido/retorno&pedido=' . $pedidoId,
                'failure' => MP_BASE_URL . '/index.php?url=pedido/retorno&pedido=' . $pedidoId,
                'pending' => MP_BASE_URL . '/index.php?url=pedido/retorno&pedido=' . $pedidoId,
            ],
            'auto_return'         => 'approved',
            'notification_url'    => MP_BASE_URL . '/index.php?url=pedido/webhook',
            'statement_descriptor' => 'NUCLEOPC',
        ];

        $resposta = $this->requisicao('POST', MP_API_URL, $payload);

        if (empty($resposta['id'])) {
            throw new Exception('Não foi possível criar a preferência de pagamento no Mercado Pago.');
        }

        return $resposta;
    }

    /** Consulta um pagamento específico pelo ID (usado no webhook). */
    public function consultarPagamento(string $pagamentoId): array
    {
        return $this->requisicao('GET', MP_PAYMENT_API_URL . $pagamentoId);
    }

    private function requisicao(string $metodo, string $url, ?array $payload = null): array
    {
        $ch = curl_init($url);
        $headers = [
            'Authorization: Bearer ' . MP_ACCESS_TOKEN,
            'Content-Type: application/json',
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        if ($metodo === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $resposta = curl_exec($ch);
        $erro = curl_error($ch);
        curl_close($ch);

        if ($resposta === false) {
            throw new Exception('Falha de comunicação com o Mercado Pago: ' . $erro);
        }

        $decodificado = json_decode($resposta, true);
        return is_array($decodificado) ? $decodificado : [];
    }
}
