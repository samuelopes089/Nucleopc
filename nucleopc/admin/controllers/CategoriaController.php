<?php
require_once __DIR__ . '/../core/Controller.php';

class CategoriaController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        $model = $this->model('Categoria');
        $this->view('categorias/index', [
            'categorias' => $model->listarComContagemProdutos(),
            'mensagem'   => $_GET['msg'] ?? null,
            'erro'       => $_GET['erro'] ?? null,
        ]);
    }

    public function novo(): void
    {
        $this->requireLogin();
        $this->view('categorias/form', ['categoria' => null, 'erro' => null]);
    }

    public function salvar(): void
    {
        $this->requireLogin();
        $model = $this->model('Categoria');
        $id = $_POST['id'] ?? null;

        $dados = [
            'nome'      => trim($_POST['nome'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
        ];

        if ($dados['nome'] === '') {
            $this->view('categorias/form', ['categoria' => $dados + ['id' => $id], 'erro' => 'O nome da categoria é obrigatório.']);
            return;
        }

        if ($id) {
            $model->atualizar((int) $id, $dados);
            $this->redirect('categoria/index?msg=atualizada');
        } else {
            $model->criar($dados);
            $this->redirect('categoria/index?msg=criada');
        }
    }

    public function editar(string $id): void
    {
        $this->requireLogin();
        $model = $this->model('Categoria');
        $categoria = $model->buscarPorId((int) $id);
        if (!$categoria) {
            $this->redirect('categoria/index');
        }
        $this->view('categorias/form', ['categoria' => $categoria, 'erro' => null]);
    }

    public function excluir(string $id): void
    {
        $this->requireLogin();
        $model = $this->model('Categoria');
        try {
            $model->excluir((int) $id);
            $this->redirect('categoria/index?msg=excluida');
        } catch (Exception $e) {
            $this->redirect('categoria/index?erro=' . urlencode($e->getMessage()));
        }
    }
}
