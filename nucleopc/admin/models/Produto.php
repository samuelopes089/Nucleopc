<?php
require_once __DIR__ . '/../config/Database.php';

class Produto
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listarTodos(): array
    {
        $sql = 'SELECT p.*, c.nome AS categoria_nome
                FROM produtos p
                LEFT JOIN categorias c ON c.id = p.categoria_id
                ORDER BY p.nome ASC';
        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM produtos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorSku(string $sku): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM produtos WHERE sku = :sku');
        $stmt->execute(['sku' => $sku]);
        return $stmt->fetch() ?: null;
    }

    public function criar(array $dados): bool
    {
        $sql = 'INSERT INTO produtos (sku, nome, descricao, categoria_id, marca, preco, custo, estoque, estoque_minimo, imagem)
                VALUES (:sku, :nome, :descricao, :categoria_id, :marca, :preco, :custo, :estoque, :estoque_minimo, :imagem)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'sku'            => $dados['sku'],
            'nome'           => $dados['nome'],
            'descricao'      => $dados['descricao'],
            'categoria_id'   => $dados['categoria_id'],
            'marca'          => $dados['marca'],
            'preco'          => $dados['preco'],
            'custo'          => $dados['custo'],
            'estoque'        => $dados['estoque'] ?? 0,
            'estoque_minimo' => $dados['estoque_minimo'] ?? 3,
            'imagem'         => $dados['imagem'] ?? 'sem-imagem.png',
        ]);
    }

    public function atualizar(int $id, array $dados): bool
    {
        $sql = 'UPDATE produtos SET sku = :sku, nome = :nome, descricao = :descricao,
                categoria_id = :categoria_id, marca = :marca, preco = :preco, custo = :custo,
                estoque_minimo = :estoque_minimo, imagem = :imagem WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'sku'            => $dados['sku'],
            'nome'           => $dados['nome'],
            'descricao'      => $dados['descricao'],
            'categoria_id'   => $dados['categoria_id'],
            'marca'          => $dados['marca'],
            'preco'          => $dados['preco'],
            'custo'          => $dados['custo'],
            'estoque_minimo' => $dados['estoque_minimo'],
            'imagem'         => $dados['imagem'],
            'id'             => $id,
        ]);
    }

    /** Soma quantidade ao estoque (usado ao confirmar uma entrada) */
    public function incrementarEstoque(int $id, int $quantidade): bool
    {
        $stmt = $this->db->prepare('UPDATE produtos SET estoque = estoque + :qtd WHERE id = :id');
        return $stmt->execute(['qtd' => $quantidade, 'id' => $id]);
    }

    /** Subtrai quantidade do estoque (usado ao confirmar uma venda) */
    public function decrementarEstoque(int $id, int $quantidade): bool
    {
        $stmt = $this->db->prepare('UPDATE produtos SET estoque = estoque - :qtd WHERE id = :id AND estoque >= :qtd_min');
        // Atenção: com prepares nativos o PDO não aceita o mesmo parâmetro nomeado duas vezes
        // (HY093); por isso a quantidade é enviada como :qtd e também como :qtd_min.
        return $stmt->execute(['qtd' => $quantidade, 'qtd_min' => $quantidade, 'id' => $id]);
    }

    public function listarEstoqueBaixo(): array
    {
        $sql = 'SELECT * FROM produtos WHERE estoque <= estoque_minimo AND ativo = 1 ORDER BY estoque ASC';
        return $this->db->query($sql)->fetchAll();
    }

    public function alterarStatus(int $id, int $ativo): bool
    {
        $stmt = $this->db->prepare('UPDATE produtos SET ativo = :ativo WHERE id = :id');
        return $stmt->execute(['ativo' => $ativo, 'id' => $id]);
    }
}
