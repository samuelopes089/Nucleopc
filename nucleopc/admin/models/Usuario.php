<?php
require_once __DIR__ . '/../config/Database.php';

class Usuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listarTodos(): array
    {
        return $this->db->query('SELECT id, nome, email, tipo, ativo, criado_em FROM usuarios ORDER BY nome ASC')->fetchAll();
    }

    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email AND ativo = 1 LIMIT 1');
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    public function buscarPorEmailQualquerStatus(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function autenticar(string $email, string $senha): ?array
    {
        $usuario = $this->buscarPorEmail($email);
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return null;
    }

    /** Cadastra um novo usuário interno (vendedor ou administrador). */
    public function criar(array $dados): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO usuarios (nome, email, senha, tipo, ativo) VALUES (:nome, :email, :senha, :tipo, 1)'
        );
        $stmt->execute([
            'nome'  => $dados['nome'],
            'email' => $dados['email'],
            'senha' => password_hash($dados['senha'], PASSWORD_DEFAULT),
            'tipo'  => $dados['tipo'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function atualizar(int $id, array $dados): bool
    {
        $sql = 'UPDATE usuarios SET nome = :nome, email = :email, tipo = :tipo WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nome'  => $dados['nome'],
            'email' => $dados['email'],
            'tipo'  => $dados['tipo'],
            'id'    => $id,
        ]);
    }

    public function redefinirSenhaAdmin(int $id, string $novaSenha): bool
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET senha = :senha WHERE id = :id');
        return $stmt->execute(['senha' => password_hash($novaSenha, PASSWORD_DEFAULT), 'id' => $id]);
    }

    public function alterarStatus(int $id, int $ativo): bool
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET ativo = :ativo WHERE id = :id');
        return $stmt->execute(['ativo' => $ativo, 'id' => $id]);
    }

    // ---------------- Recuperação de senha ----------------

    /** Gera e grava um token de recuperação válido por 1 hora. Retorna o token (ou null se e-mail não existir). */
    public function gerarTokenRecuperacao(string $email): ?string
    {
        $usuario = $this->buscarPorEmailQualquerStatus($email);
        if (!$usuario) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->db->prepare('UPDATE usuarios SET reset_token = :token, reset_token_expira = :expira WHERE id = :id');
        $stmt->execute(['token' => $token, 'expira' => $expira, 'id' => $usuario['id']]);

        return $token;
    }

    public function buscarPorTokenValido(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM usuarios WHERE reset_token = :token AND reset_token_expira >= NOW() LIMIT 1'
        );
        $stmt->execute(['token' => $token]);
        return $stmt->fetch() ?: null;
    }

    public function redefinirSenhaComToken(string $token, string $novaSenha): bool
    {
        $usuario = $this->buscarPorTokenValido($token);
        if (!$usuario) {
            return false;
        }
        $stmt = $this->db->prepare(
            'UPDATE usuarios SET senha = :senha, reset_token = NULL, reset_token_expira = NULL WHERE id = :id'
        );
        return $stmt->execute(['senha' => password_hash($novaSenha, PASSWORD_DEFAULT), 'id' => $usuario['id']]);
    }
}
