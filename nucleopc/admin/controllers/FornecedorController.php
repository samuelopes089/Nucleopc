<?php
require_once __DIR__ . '/../core/Controller.php';

class FornecedorController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        $model = $this->model('Fornecedor');
        $this->view('fornecedores/index', [
            'fornecedores' => $model->listarTodos(),
            'mensagem'     => $_GET['msg'] ?? null,
        ]);
    }

    public function novo(): void
    {
        $this->requireLogin();
        $this->view('fornecedores/form', ['fornecedor' => null, 'erro' => null]);
    }

    public function salvar(): void
    {
        $this->requireLogin();
        $model = $this->model('Fornecedor');

        $dados = [
            'nome'     => trim($_POST['nome'] ?? ''),
            'cnpj'     => trim($_POST['cnpj'] ?? ''),
            'telefone' => trim($_POST['telefone'] ?? ''),
            'email'    => trim($_POST['email'] ?? ''),
            'cep'      => trim($_POST['cep'] ?? ''),
            'endereco' => trim($_POST['endereco'] ?? ''),
        ];

        if ($dados['nome'] === '') {
            $this->view('fornecedores/form', ['fornecedor' => $dados, 'erro' => 'O nome é obrigatório.']);
            return;
        }

        // Evita cadastro repetido com o mesmo CNPJ
        if ($dados['cnpj'] !== '' && $model->buscarPorCnpj($dados['cnpj'])) {
            $this->view('fornecedores/form', ['fornecedor' => $dados, 'erro' => 'Já existe um fornecedor cadastrado com este CNPJ.']);
            return;
        }

        $id = $_POST['id'] ?? null;
        if ($id) {
            $model->atualizar((int) $id, $dados);
            $this->redirect('fornecedor/index?msg=atualizado');
        } else {
            $model->criar($dados);
            $this->redirect('fornecedor/index?msg=criado');
        }
    }

    public function editar(string $id): void
    {
        $this->requireLogin();
        $model = $this->model('Fornecedor');
        $fornecedor = $model->buscarPorId((int) $id);
        if (!$fornecedor) {
            $this->redirect('fornecedor/index');
        }
        $this->view('fornecedores/form', ['fornecedor' => $fornecedor, 'erro' => null]);
    }

    public function inativar(string $id): void
    {
        $this->requireLogin();
        $model = $this->model('Fornecedor');
        $model->alterarStatus((int) $id, 'inativo');
        $this->redirect('fornecedor/index?msg=inativado');
    }

    public function ativar(string $id): void
    {
        $this->requireLogin();
        $model = $this->model('Fornecedor');
        $model->alterarStatus((int) $id, 'ativo');
        $this->redirect('fornecedor/index?msg=ativado');
    }

    /**
     * Consulta o CEP via AJAX e devolve o endereço em JSON.
     * (No front-end a busca real do CEP é feita pela ViaCEP - ver assets/js/admin.js)
     */
    public function buscarCep(string $cep): void
    {
        header('Content-Type: application/json');
        echo json_encode(['cep' => $cep]);
    }
}
