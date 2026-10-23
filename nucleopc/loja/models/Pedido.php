<?php
require_once __DIR__ . '/../config/Database.php';

class Pedido
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Cria o pedido online a partir dos itens do carrinho, dentro de uma
     * transação: cabeçalho + itens + reserva de estoque. O pedido nasce
     * com status "aguardando_pagamento" — só passa a "confirmado" quando
     * o Mercado Pago confirma o pagamento (ver PedidoController::webhook).
     */
    public function criarPedidoOnline(int $clienteId, array $itensCarrinho, string $enderecoEntrega): int
    {
        $this->db->beginTransaction();
        try {
            $valorTotal = 0;
            foreach ($itensCarrinho as $item) {
                $valorTotal += $item['preco'] * $item['quantidade'];
            }

            $stmt = $this->db->prepare(
                'INSERT INTO pedidos (cliente_id, origem, status, valor_total, endereco_entrega, forma_pagamento)
                 VALUES (:cliente_id, "online", "aguardando_pagamento", :valor_total, :endereco, "mercado_pago")'
            );
            $stmt->execute([
                'cliente_id'  => $clienteId,
                'valor_total' => $valorTotal,
                'endereco'    => $enderecoEntrega,
            ]);
            $pedidoId = (int) $this->db->lastInsertId();

            $stmtItem = $this->db->prepare(
                'INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario)
                 VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario)'
            );
            $stmtEstoque = $this->db->prepare(
                'UPDATE produtos SET estoque = estoque - :qtd WHERE id = :id AND estoque >= :qtd_min'
            );

            foreach ($itensCarrinho as $item) {
                $stmtItem->execute([
                    'pedido_id'      => $pedidoId,
                    'produto_id'     => $item['produto_id'],
                    'quantidade'     => $item['quantidade'],
                    'preco_unitario' => $item['preco'],
                ]);
                // A reserva de estoque acontece já na criação do pedido (antes do
                // pagamento ser aprovado) para não vender o mesmo item duas vezes
                // enquanto o cliente está no checkout do Mercado Pago. Caso o
                // pagamento seja recusado/cancelado, o estoque é devolvido em
                // restaurarEstoque().
                $stmtEstoque->execute([
                    'qtd'     => $item['quantidade'],
                    'qtd_min' => $item['quantidade'], // parâmetro nomeado não pode se repetir (PDO nativo)
                    'id'      => $item['produto_id'],
                ]);
                if ($stmtEstoque->rowCount() === 0) {
                    throw new Exception('Estoque insuficiente para "' . $item['nome'] . '".');
                }
            }

            $this->db->commit();
            return $pedidoId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Grava o ID da preferência de pagamento gerada no Mercado Pago. */
    public function salvarPreferencia(int $pedidoId, string $preferenciaId): bool
    {
        $stmt = $this->db->prepare('UPDATE pedidos SET preferencia_id = :pref WHERE id = :id');
        return $stmt->execute(['pref' => $preferenciaId, 'id' => $pedidoId]);
    }

    /**
     * Atualiza o pedido com o resultado do pagamento vindo do Mercado Pago.
     * status esperado: "confirmado" (approved), "cancelado" (rejected/cancelled)
     * ou mantém "aguardando_pagamento" (pending/in_process).
     */
    public function atualizarStatusPagamento(int $pedidoId, string $status, ?string $pagamentoId, string $pagamentoStatusOriginal): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE pedidos SET status = :status, pagamento_id = :pagamento_id, pagamento_status = :pagamento_status
             WHERE id = :id'
        );
        $ok = $stmt->execute([
            'status'           => $status,
            'pagamento_id'     => $pagamentoId,
            'pagamento_status' => $pagamentoStatusOriginal,
            'id'               => $pedidoId,
        ]);

        if ($status === 'cancelado') {
            $this->restaurarEstoque($pedidoId);
        }

        return $ok;
    }

    /** Devolve ao estoque os itens de um pedido que não teve o pagamento aprovado. */
    public function restaurarEstoque(int $pedidoId): void
    {
        $itens = $this->itensDoPedido($pedidoId);
        $stmt = $this->db->prepare('UPDATE produtos SET estoque = estoque + :qtd WHERE id = :id');
        foreach ($itens as $item) {
            $stmt->execute(['qtd' => $item['quantidade'], 'id' => $item['produto_id']]);
        }
    }

    public function listarPorCliente(int $clienteId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM pedidos WHERE cliente_id = :id ORDER BY id DESC');
        $stmt->execute(['id' => $clienteId]);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM pedidos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function itensDoPedido(int $pedidoId): array
    {
        $sql = 'SELECT pi.*, p.nome, p.sku, p.imagem
                FROM pedido_itens pi JOIN produtos p ON p.id = pi.produto_id
                WHERE pi.pedido_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $pedidoId]);
        return $stmt->fetchAll();
    }
}
