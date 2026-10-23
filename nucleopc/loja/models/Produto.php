<?php
require_once __DIR__ . '/../config/Database.php';

class Produto
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Lista produtos ativos do catálogo, com filtros opcionais
     * de categoria, busca por nome e ordenação.
     */
    public function listarCatalogo(?int $categoriaId = null, ?string $busca = null, string $ordem = 'relevancia'): array
    {
        $sql = 'SELECT p.*, c.nome AS categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE p.ativo = 1 AND p.estoque > 0';
        $params = [];

        if ($categoriaId) {
            $sql .= ' AND p.categoria_id = :categoria_id';
            $params['categoria_id'] = $categoriaId;
        }
        if ($busca) {
            // Um nome de parâmetro por ocorrência: o PDO nativo (EMULATE_PREPARES = false)
            // não aceita reutilizar o mesmo parâmetro nomeado na consulta (erro HY093).
            $sql .= ' AND (p.nome LIKE :busca_nome OR p.marca LIKE :busca_marca OR p.descricao LIKE :busca_desc)';
            $termo = '%' . $busca . '%';
            $params['busca_nome']  = $termo;
            $params['busca_marca'] = $termo;
            $params['busca_desc']  = $termo;
        }

        // Escolhe a ordem dos produtos conforme a opção selecionada.
        switch ($ordem) {
            case 'menor_preco':
                $sql .= ' ORDER BY p.preco ASC';
                break;
            case 'maior_preco':
                $sql .= ' ORDER BY p.preco DESC';
                break;
            default:
                $sql .= ' ORDER BY p.id DESC';
                break;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $sql = 'SELECT p.*, c.nome AS categoria_nome
                FROM produtos p LEFT JOIN categorias c ON c.id = p.categoria_id
                WHERE p.id = :id AND p.ativo = 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function relacionados(int $categoriaId, int $excluirId, int $limite = 4): array
    {
        $sql = 'SELECT * FROM produtos WHERE categoria_id = :cat AND id != :id AND ativo = 1 AND estoque > 0 LIMIT :limite';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('cat', $categoriaId, PDO::PARAM_INT);
        $stmt->bindValue('id', $excluirId, PDO::PARAM_INT);
        $stmt->bindValue('limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function maisVendidos(int $limite = 8): array
    {
        $sql = 'SELECT p.*, COALESCE(SUM(pi.quantidade),0) AS total_vendido
                FROM produtos p
                LEFT JOIN pedido_itens pi ON pi.produto_id = p.id
                WHERE p.ativo = 1 AND p.estoque > 0
                GROUP BY p.id
                ORDER BY total_vendido DESC, p.id DESC
                LIMIT :limite';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
