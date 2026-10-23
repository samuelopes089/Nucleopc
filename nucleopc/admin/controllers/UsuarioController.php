<?php
require_once __DIR__ . '/../core/Controller.php';

class UsuarioController extends Controller
{
    /** Apenas administradores podem gerenciar contas de acesso ao painel. */
    public function index(): void
    {
        $this->requireAdmin();
        $model = $this->model('Usuario');
        $this->view('usuarios/index', [
            'usuarios' => $model->listarTodos(),
            'mensagem' => $_GET['msg'] ?? null,
        ]);
    }

    public function novo(): void
    {
        $this->requireAdmin();
        $this->view('usuarios/form', ['usuario' => null, 'erro' => null]);
    }

    public function salvar(): void
    {
        $this->requireAdmin();
        $model = $this->model('Usuario');

        $id = $_POST['id'] ?? null;
        $dados = [
            'nome'  => trim($_POST['nome'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'tipo'  => ($_POST['tipo'] ?? 'vendedor') === 'admin' ? 'admin' : 'vendedor',
        ];
        $senha = $_POST['senha'] ?? '';
        $confirmacao = $_POST['confirmacao'] ?? '';

        if ($dados['nome'] === '' || $dados['email'] === '') {
            $this->view('usuarios/form', ['usuario' => $dados + ['id' => $id], 'erro' => 'Nome e e-mail são obrigatórios.']);
            return;
        }

        $existente = $model->buscarPorEmailQualquerStatus($dados['email']);
        if ($existente && (!$id || (int) $existente['id'] !== (int) $id)) {
            $this->view('usuarios/form', ['usuario' => $dados + ['id' => $id], 'erro' => 'Já existe um usuário cadastrado com este e-mail.']);
            return;
        }

        if ($id) {
            // Edição: senha é opcional (só altera se preenchida)
            if ($senha !== '' || $confirmacao !== '') {
                if (strlen($senha) < 6) {
                    $this->view('usuarios/form', ['usuario' => $dados + ['id' => $id], 'erro' => 'A nova senha deve ter pelo menos 6 caracteres.']);
                    return;
                }
                if ($senha !== $confirmacao) {
                    $this->view('usuarios/form', ['usuario' => $dados + ['id' => $id], 'erro' => 'A confirmação de senha não confere.']);
                    return;
                }
                $model->redefinirSenhaAdmin((int) $id, $senha);
            }
            $model->atualizar((int) $id, $dados);
            $this->redirect('usuario/index?msg=atualizado');
        } else {
            if (strlen($senha) < 6) {
                $this->view('usuarios/form', ['usuario' => $dados, 'erro' => 'A senha deve ter pelo menos 6 caracteres.']);
                return;
            }
            if ($senha !== $confirmacao) {
                $this->view('usuarios/form', ['usuario' => $dados, 'erro' => 'A confirmação de senha não confere.']);
                return;
            }
            $model->criar($dados + ['senha' => $senha]);
            $this->redirect('usuario/index?msg=criado');
        }
    }

    public function editar(string $id): void
    {
        $this->requireAdmin();
        $model = $this->model('Usuario');
        $usuario = $model->buscarPorId((int) $id);
        if (!$usuario) {
            $this->redirect('usuario/index');
        }
        $this->view('usuarios/form', ['usuario' => $usuario, 'erro' => null]);
    }

    public function inativar(string $id): void
    {
        $this->requireAdmin();
        if ((int) $id === (int) $_SESSION['usuario_id']) {
            $this->redirect('usuario/index?msg=erro_auto_inativar');
            return;
        }
        $this->model('Usuario')->alterarStatus((int) $id, 0);
        $this->redirect('usuario/index?msg=inativado');
    }

    public function ativar(string $id): void
    {
        $this->requireAdmin();
        $this->model('Usuario')->alterarStatus((int) $id, 1);
        $this->redirect('usuario/index?msg=ativado');
    }
}
