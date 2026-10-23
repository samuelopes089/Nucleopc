<?php
require_once __DIR__ . '/../core/Controller.php';

class AuthController extends Controller
{
    /** Segurança extra: qualquer acesso sem ação definida cai no login. */
    public function index(): void
    {
        $this->redirect('auth/login');
    }

    public function login(): void
    {
        if (!empty($_SESSION['usuario_id'])) {
            $this->redirect('dashboard');
        }

        $erro = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            $usuarioModel = $this->model('Usuario');
            $usuario = $usuarioModel->autenticar($email, $senha);

            if ($usuario) {
                $_SESSION['usuario_id']   = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_tipo'] = $usuario['tipo'];
                $this->redirect('dashboard');
            } else {
                $erro = 'E-mail ou senha inválidos.';
            }
        }

        $this->view('auth/login', ['erro' => $erro]);
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: index.php?url=auth/login');
        exit;
    }

    /**
     * Cadastro público de conta no Painel Admin. Por segurança, toda conta
     * criada por aqui nasce com o perfil "vendedor" — promover alguém a
     * administrador é uma ação feita por um admin já autenticado, em
     * Usuários (usuario/editar).
     */
    public function cadastro(): void
    {
        if (!empty($_SESSION['usuario_id'])) {
            $this->redirect('dashboard');
        }

        $erro = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioModel = $this->model('Usuario');

            $nome  = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $confirmacao = $_POST['confirmacao'] ?? '';

            if ($nome === '' || $email === '' || $senha === '') {
                $erro = 'Preencha nome, e-mail e senha.';
            } elseif (strlen($senha) < 6) {
                $erro = 'A senha deve ter pelo menos 6 caracteres.';
            } elseif ($senha !== $confirmacao) {
                $erro = 'A confirmação de senha não confere.';
            } elseif ($usuarioModel->buscarPorEmailQualquerStatus($email)) {
                $erro = 'Já existe uma conta cadastrada com este e-mail.';
            } else {
                $id = $usuarioModel->criar([
                    'nome'  => $nome,
                    'email' => $email,
                    'senha' => $senha,
                    'tipo'  => 'vendedor',
                ]);
                $_SESSION['usuario_id']   = $id;
                $_SESSION['usuario_nome'] = $nome;
                $_SESSION['usuario_tipo'] = 'vendedor';
                $this->redirect('dashboard');
                return;
            }
        }

        $this->view('auth/cadastro', ['erro' => $erro]);
    }

    /** Formulário para solicitar o link de redefinição de senha. */
    public function esqueciSenha(): void
    {
        $mensagem = null;
        $linkTeste = null; // exibido apenas em ambiente de desenvolvimento (ver config/mail.php)

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $usuarioModel = $this->model('Usuario');
            $token = $usuarioModel->gerarTokenRecuperacao($email);

            if ($token) {
                $link = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/index.php?url=auth/redefinirSenha/' . $token;
                $corpo = '<p>Você solicitou a redefinição da sua senha no painel Núcleo PC.</p>'
                       . '<p><a href="' . $link . '">Clique aqui para criar uma nova senha</a> (válido por 1 hora).</p>'
                       . '<p>Se você não solicitou, ignore este e-mail.</p>';
                enviarEmail($email, 'Recuperação de senha — Núcleo PC', $corpo);
                $linkTeste = $link;
            }

            // Mensagem genérica sempre, para não revelar quais e-mails existem na base.
            $mensagem = 'Se o e-mail informado estiver cadastrado, enviamos um link de redefinição de senha.';
        }

        $this->view('auth/esqueci_senha', ['mensagem' => $mensagem, 'linkTeste' => $linkTeste]);
    }

    /** Formulário para definir uma nova senha a partir de um token válido. */
    public function redefinirSenha(string $token): void
    {
        $usuarioModel = $this->model('Usuario');
        $usuario = $usuarioModel->buscarPorTokenValido($token);

        if (!$usuario) {
            $this->view('auth/redefinir_senha', ['token' => $token, 'valido' => false, 'erro' => null]);
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
                $usuarioModel->redefinirSenhaComToken($token, $senha);
                $this->redirect('auth/login?msg=senha_redefinida');
                return;
            }
        }

        $this->view('auth/redefinir_senha', ['token' => $token, 'valido' => true, 'erro' => $erro]);
    }
}
