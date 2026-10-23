<?php
require_once __DIR__ . '/../config/Database.php';

class Entrada
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listarRecentes(int $limite = 10): array
    {
        $sql = 'SELECT e.*, f.nome AS fornecedor_nome
                FROM entradas e
                JOIN fornecedores f ON f.id = e.fornecedor_id
                ORDER BY e.id DESC LIMIT :limite';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Registra uma entrada de mercadoria com seus itens dentro de uma
     * transação: cria o cabeçalho, os itens e atualiza o estoque.
     *
     * @param array $itens cada item: ['produto_id' => int, 'quantidade' => int, 'custo_unitario' => float]
     */
    public function registrar(int $fornecedorId, int $usuarioId, array $itens): int
    {
        $this->db->beginTransaction();
        try {
            $valorTotal = 0;
            foreach ($itens as $item) {
                $valorTotal += $item['quantidade'] * $item['custo_unitario'];
            }

            $stmt = $this->db->prepare(
                'INSERT INTO entradas (fornecedor_id, usuario_id, data_entrada, valor_total, status)
                 VALUES (:fornecedor_id, :usuario_id, CURDATE(), :valor_total, "confirmada")'
            );
            $stmt->execute([
                'fornecedor_id' => $fornecedorId,
                'usuario_id'    => $usuarioId,
                'valor_total'   => $valorTotal,
            ]);
            $entradaId = (int) $this->db->lastInsertId();

            $stmtItem = $this->db->prepare(
                'INSERT INTO entrada_itens (entrada_id, produto_id, quantidade, custo_unitario)
                 VALUES (:entrada_id, :produto_id, :quantidade, :custo_unitario)'
            );
            $stmtEstoque = $this->db->prepare(
                'UPDATE produtos SET estoque = estoque + :qtd WHERE id = :id'
            );

            foreach ($itens as $item) {
                $stmtItem->execute([
                    'entrada_id'     => $entradaId,
                    'produto_id'     => $item['produto_id'],
                    'quantidade'     => $item['quantidade'],
                    'custo_unitario' => $item['custo_unitario'],
                ]);
                $stmtEstoque->execute([
                    'qtd' => $item['quantidade'],
                    'id'  => $item['produto_id'],
                ]);
            }

            $this->db->commit();
            return $entradaId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
