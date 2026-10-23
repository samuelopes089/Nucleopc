<?php
require_once __DIR__ . '/../config/Database.php';

class Cliente
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM clientes WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM clientes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function autenticar(string $email, string $senha): ?array
    {
        $cliente = $this->buscarPorEmail($email);
        if ($cliente && password_verify($senha, $cliente['senha'])) {
            return $cliente;
        }
        return null;
    }

    public function cadastrar(array $dados): int
    {
        $sql = 'INSERT INTO clientes (nome, email, senha, cpf, telefone, cep, endereco, numero, complemento, bairro, cidade, uf)
                VALUES (:nome, :email, :senha, :cpf, :telefone, :cep, :endereco, :numero, :complemento, :bairro, :cidade, :uf)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nome'        => $dados['nome'],
            'email'       => $dados['email'],
            'senha'       => password_hash($dados['senha'], PASSWORD_DEFAULT),
            'cpf'         => $dados['cpf'] ?? null,
            'telefone'    => $dados['telefone'] ?? null,
            'cep'         => $dados['cep'] ?? null,
            'endereco'    => $dados['endereco'] ?? null,
            'numero'      => $dados['numero'] ?? null,
            'complemento' => $dados['complemento'] ?? null,
            'bairro'      => $dados['bairro'] ?? null,
            'cidade'      => $dados['cidade'] ?? null,
            'uf'          => $dados['uf'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    // ---------------- Recuperação de senha ----------------

    /** Gera e grava um token de recuperação válido por 1 hora. Retorna o token (ou null se e-mail não existir). */
    public function gerarTokenRecuperacao(string $email): ?string
    {
        $cliente = $this->buscarPorEmail($email);
        if (!$cliente) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->db->prepare('UPDATE clientes SET reset_token = :token, reset_token_expira = :expira WHERE id = :id');
        $stmt->execute(['token' => $token, 'expira' => $expira, 'id' => $cliente['id']]);

        return $token;
    }

    public function buscarPorTokenValido(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM clientes WHERE reset_token = :token AND reset_token_expira >= NOW() LIMIT 1'
        );
        $stmt->execute(['token' => $token]);
        return $stmt->fetch() ?: null;
    }

    public function redefinirSenhaComToken(string $token, string $novaSenha): bool
    {
        $cliente = $this->buscarPorTokenValido($token);
        if (!$cliente) {
            return false;
        }
        $stmt = $this->db->prepare(
            'UPDATE clientes SET senha = :senha, reset_token = NULL, reset_token_expira = NULL WHERE id = :id'
        );
        return $stmt->execute(['senha' => password_hash($novaSenha, PASSWORD_DEFAULT), 'id' => $cliente['id']]);
    }
}
