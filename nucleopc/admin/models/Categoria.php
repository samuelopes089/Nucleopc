<?php
require_once __DIR__ . '/../config/Database.php';

class Categoria
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listarTodas(): array
    {
        return $this->db->query('SELECT * FROM categorias ORDER BY nome ASC')->fetchAll();
    }

    /** Lista categorias já com a contagem de produtos vinculados (para a tela de gestão). */
    public function listarComContagemProdutos(): array
    {
        $sql = 'SELECT c.*, COUNT(p.id) AS total_produtos
                FROM categorias c
                LEFT JOIN produtos p ON p.categoria_id = c.id
                GROUP BY c.id
                ORDER BY c.nome ASC';
        return $this->db->query($sql)->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categorias WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorSlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categorias WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    /** Gera um slug amigável e único a partir do nome (ex.: "Placas de Vídeo" -> "placas-de-video"). */
    public function gerarSlug(string $nome, ?int $ignorarId = null): string
    {
        $base = strtolower($nome);
        $base = preg_replace('/[àáâãäå]/u', 'a', $base);
        $base = preg_replace('/[èéêë]/u', 'e', $base);
        $base = preg_replace('/[ìíîï]/u', 'i', $base);
        $base = preg_replace('/[òóôõö]/u', 'o', $base);
        $base = preg_replace('/[ùúûü]/u', 'u', $base);
        $base = preg_replace('/[ç]/u', 'c', $base);
        $base = preg_replace('/[^a-z0-9]+/', '-', $base);
        $base = trim($base, '-');

        $slug = $base;
        $sufixo = 2;
        while (true) {
            $existente = $this->buscarPorSlug($slug);
            if (!$existente || (int) $existente['id'] === (int) $ignorarId) {
                break;
            }
            $slug = $base . '-' . $sufixo;
            $sufixo++;
        }
        return $slug;
    }

    public function criar(array $dados): int
    {
        $slug = $this->gerarSlug($dados['nome']);
        $stmt = $this->db->prepare(
            'INSERT INTO categorias (nome, slug, descricao) VALUES (:nome, :slug, :descricao)'
        );
        $stmt->execute([
            'nome'      => $dados['nome'],
            'slug'      => $slug,
            'descricao' => $dados['descricao'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function atualizar(int $id, array $dados): bool
    {
        $slug = $this->gerarSlug($dados['nome'], $id);
        $stmt = $this->db->prepare(
            'UPDATE categorias SET nome = :nome, slug = :slug, descricao = :descricao WHERE id = :id'
        );
        return $stmt->execute([
            'nome'      => $dados['nome'],
            'slug'      => $slug,
            'descricao' => $dados['descricao'],
            'id'        => $id,
        ]);
    }

    /**
     * Exclui uma categoria. Lança uma exceção amigável se ainda houver
     * produtos vinculados (o banco bloqueia a exclusão via chave estrangeira).
     */
    public function excluir(int $id): bool
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM categorias WHERE id = :id');
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            if ((int) $e->getCode() === 23000 || str_contains($e->getMessage(), 'foreign key')) {
                throw new Exception('Não é possível excluir: existem produtos cadastrados nesta categoria. Mova-os para outra categoria antes de excluir.');
            }
            throw $e;
        }
    }
}
