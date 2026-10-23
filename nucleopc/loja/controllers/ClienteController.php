<?php
require_once __DIR__ . '/../core/Controller.php';

class ClienteController extends Controller
{
    /** Segurança extra: qualquer acesso sem ação definida cai no login. */
    public function index(): void
    {
        $this->redirect('cliente/login');
    }

    public function login(): void
    {
        if (!empty($_SESSION['cliente_id'])) {
            $this->redirect('home/index');
        }

        $erro = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            $clienteModel = $this->model('Cliente');
            $cliente = $clienteModel->autenticar($email, $senha);

            if ($cliente) {
                $_SESSION['cliente_id']   = $cliente['id'];
                $_SESSION['cliente_nome'] = $cliente['nome'];
                $destino = $_GET['redirect'] ?? 'home/index';
                $this->redirect($destino);
            } else {
                $erro = 'E-mail ou senha inválidos.';
            }
        }

        $this->view('cliente/login', ['erro' => $erro]);
    }

    public function cadastro(): void
    {
        $erro = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clienteModel = $this->model('Cliente');

            $dados = [
                'nome'        => trim($_POST['nome'] ?? ''),
                'email'       => trim($_POST['email'] ?? ''),
                'senha'       => $_POST['senha'] ?? '',
                'cpf'         => trim($_POST['cpf'] ?? ''),
                'telefone'    => trim($_POST['telefone'] ?? ''),
                'cep'         => trim($_POST['cep'] ?? ''),
                'endereco'    => trim($_POST['endereco'] ?? ''),
                'numero'      => trim($_POST['numero'] ?? ''),
                'complemento' => trim($_POST['complemento'] ?? ''),
                'bairro'      => trim($_POST['bairro'] ?? ''),
                'cidade'      => trim($_POST['cidade'] ?? ''),
                'uf'          => trim($_POST['uf'] ?? ''),
            ];

            if ($dados['nome'] === '' || $dados['email'] === '' || $dados['senha'] === '') {
                $erro = 'Preencha nome, e-mail e senha.';
            } elseif ($clienteModel->buscarPorEmail($dados['email'])) {
                $erro = 'Já existe uma conta cadastrada com este e-mail.';
            } else {
                $id = $clienteModel->cadastrar($dados);
                $_SESSION['cliente_id']   = $id;
                $_SESSION['cliente_nome'] = $dados['nome'];
                $this->redirect('home/index');
            }
        }

        $this->view('cliente/cadastro', ['erro' => $erro]);
    }

    public function logout(): void
    {
        unset($_SESSION['cliente_id'], $_SESSION['cliente_nome']);
        $this->redirect('home/index');
    }

    /** Formulário para solicitar o link de redefinição de senha. */
    public function esqueciSenha(): void
    {
        $mensagem = null;
        $linkTeste = null; // exibido apenas em ambiente de desenvolvimento (ver config/mail.php)

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $clienteModel = $this->model('Cliente');
            $token = $clienteModel->gerarTokenRecuperacao($email);

            if ($token) {
                $link = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/index.php?url=cliente/redefinirSenha/' . $token;
                $corpo = '<p>Você solicitou a redefinição da sua senha na Núcleo PC.</p>'
                       . '<p><a href="' . $link . '">Clique aqui para criar uma nova senha</a> (válido por 1 hora).</p>'
                       . '<p>Se você não solicitou, ignore este e-mail.</p>';
                enviarEmail($email, 'Recuperação de senha — Núcleo PC', $corpo);
                $linkTeste = $link;
            }

            $mensagem = 'Se o e-mail informado estiver cadastrado, enviamos um link de redefinição de senha.';
        }

        $this->view('cliente/esqueci_senha', ['mensagem' => $mensagem, 'linkTeste' => $linkTeste]);
    }

    /** Formulário para definir uma nova senha a partir de um token válido. */
    public function redefinirSenha(string $token): void
    {
        $clienteModel = $this->model('Cliente');
        $cliente = $clienteModel->buscarPorTokenValido($token);

        if (!$cliente) {
            $this->view('cliente/redefinir_senha', ['token' => $token, 'valido' => false, 'erro' => null]);
            return;
        }

        $erro = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $senha = $_POST['senha'] ?? '';
            $confirmacao = $_POST['confirmacao'] ?? '';

            if (strlen($senha) < 6) {
                $erro = 'A senha deve ter pelo menos 6 caracteres.';
            } elseif ($senha !== $confirmacao) {
                $erro = 'A confirmação não confere com a nova senha.';
            } else {
                $clienteModel->redefinirSenhaComToken($token, $senha);
                $this->redirect('cliente/login?msg=senha_redefinida');
                return;
            }
        }

        $this->view('cliente/redefinir_senha', ['token' => $token, 'valido' => true, 'erro' => $erro]);
    }

    public function meusPedidos(): void
    {
        $this->requireCliente();
        $pedidoModel = $this->model('Pedido');
        $this->view('cliente/pedidos', [
            'pedidos' => $pedidoModel->listarPorCliente((int) $_SESSION['cliente_id']),
        ]);
    }
}
