<?php
require_once __DIR__ . '/../config/Database.php';

class Pedido
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listarRecentes(int $limite = 20): array
    {
        $sql = 'SELECT p.*, c.nome AS cliente_nome, u.nome AS vendedor_nome
                FROM pedidos p
                LEFT JOIN clientes c ON c.id = p.cliente_id
                LEFT JOIN usuarios u ON u.id = p.usuario_id
                ORDER BY p.id DESC LIMIT :limite';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = 'SELECT p.*, c.nome AS cliente_nome, u.nome AS vendedor_nome
                FROM pedidos p
                LEFT JOIN clientes c ON c.id = p.cliente_id
                LEFT JOIN usuarios u ON u.id = p.usuario_id
                WHERE p.id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function itensDoPedido(int $pedidoId): array
    {
        $sql = 'SELECT pi.*, pr.nome AS produto_nome, pr.sku
                FROM pedido_itens pi
                JOIN produtos pr ON pr.id = pi.produto_id
                WHERE pi.pedido_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $pedidoId]);
        return $stmt->fetchAll();
    }

    /**
     * Registra uma venda feita no balcão da loja física.
     * @param array $itens cada item: ['produto_id' => int, 'quantidade' => int, 'preco_unitario' => float]
     */
    public function registrarVendaBalcao(?int $clienteId, int $usuarioId, array $itens): int
    {
        $this->db->beginTransaction();
        try {
            $valorTotal = 0;
            foreach ($itens as $item) {
                $valorTotal += $item['quantidade'] * $item['preco_unitario'];
            }

            $stmt = $this->db->prepare(
                'INSERT INTO pedidos (cliente_id, usuario_id, origem, status, valor_total, forma_pagamento)
                 VALUES (:cliente_id, :usuario_id, "loja_fisica", "confirmado", :valor_total, :forma_pagamento)'
            );
            $stmt->execute([
                'cliente_id'      => $clienteId,
                'usuario_id'      => $usuarioId,
                'valor_total'     => $valorTotal,
                'forma_pagamento' => 'balcao',
            ]);
            $pedidoId = (int) $this->db->lastInsertId();

            $stmtItem = $this->db->prepare(
                'INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario)
                 VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario)'
            );
            $stmtEstoque = $this->db->prepare(
                'UPDATE produtos SET estoque = estoque - :qtd WHERE id = :id AND estoque >= :qtd_min'
            );

            foreach ($itens as $item) {
                $stmtItem->execute([
                    'pedido_id'      => $pedidoId,
                    'produto_id'     => $item['produto_id'],
                    'quantidade'     => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario'],
                ]);
                $ok = $stmtEstoque->execute([
                    'qtd'     => $item['quantidade'],
                    'qtd_min' => $item['quantidade'], // parâmetro nomeado não pode se repetir (PDO nativo)
                    'id'      => $item['produto_id'],
                ]);
                if ($stmtEstoque->rowCount() === 0) {
                    throw new Exception('Quantidade indisponível em estoque para o produto ID ' . $item['produto_id']);
                }
            }

            $this->db->commit();
            return $pedidoId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function emitirNotaFiscal(int $pedidoId): array
    {
        $pedido = $this->buscarPorId($pedidoId);
        if (!$pedido) {
            throw new Exception('Pedido não encontrado.');
        }

        $stmt = $this->db->prepare('SELECT * FROM notas_fiscais WHERE pedido_id = :id');
        $stmt->execute(['id' => $pedidoId]);
        $notaExistente = $stmt->fetch();
        if ($notaExistente) {
            return $notaExistente;
        }

        $proximoNumero = (int) $this->db->query('SELECT COALESCE(MAX(numero), 0) + 1 FROM notas_fiscais')->fetchColumn();

        $stmt = $this->db->prepare(
            'INSERT INTO notas_fiscais (pedido_id, numero, serie, valor_total)
             VALUES (:pedido_id, :numero, 1, :valor_total)'
        );
        $stmt->execute([
            'pedido_id'   => $pedidoId,
            'numero'      => $proximoNumero,
            'valor_total' => $pedido['valor_total'],
        ]);

        $stmt = $this->db->prepare('SELECT * FROM notas_fiscais WHERE id = :id');
        $stmt->execute(['id' => $this->db->lastInsertId()]);
        return $stmt->fetch();
    }

    // ---------------- Relatórios ----------------

    public function produtosMaisVendidos(int $limite = 10): array
    {
        $sql = 'SELECT pr.nome, SUM(pi.quantidade) AS qtde_vendida
                FROM pedido_itens pi
                JOIN produtos pr ON pr.id = pi.produto_id
                JOIN pedidos p ON p.id = pi.pedido_id
                WHERE p.status != "cancelado"
                GROUP BY pi.produto_id
                ORDER BY qtde_vendida DESC
                LIMIT :limite';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function totalVendasPorOrigem(): array
    {
        $sql = 'SELECT origem, COUNT(*) AS total_pedidos, COALESCE(SUM(valor_total),0) AS valor_total
                FROM pedidos WHERE status != "cancelado" GROUP BY origem';
        return $this->db->query($sql)->fetchAll();
    }

    public function fornecedoresResumo(): array
    {
        $sql = 'SELECT f.nome, COUNT(e.id) AS entradas, COALESCE(SUM(e.valor_total),0) AS valor_total
                FROM fornecedores f
                LEFT JOIN entradas e ON e.fornecedor_id = f.id
                GROUP BY f.id
                ORDER BY valor_total DESC';
        return $this->db->query($sql)->fetchAll();
    }
}
