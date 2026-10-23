<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../config/Database.php';

class ContatoController extends Controller
{
    public function index(): void
    {
        $this->view('contato/index', ['enviado' => false, 'erro' => null]);
    }

    public function enviar(): void
    {
        $nome     = trim($_POST['nome'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $assunto  = trim($_POST['assunto'] ?? '');
        $mensagem = trim($_POST['mensagem'] ?? '');

        if ($nome === '' || $email === '' || $mensagem === '') {
            $this->view('contato/index', ['enviado' => false, 'erro' => 'Preencha nome, e-mail e mensagem.']);
            return;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare(
            'INSERT INTO contatos (nome, email, assunto, mensagem) VALUES (:nome, :email, :assunto, :mensagem)'
        );
        $stmt->execute([
            'nome'     => $nome,
            'email'    => $email,
            'assunto'  => $assunto,
            'mensagem' => $mensagem,
        ]);

        $this->view('contato/index', ['enviado' => true, 'erro' => null]);
    }
}
