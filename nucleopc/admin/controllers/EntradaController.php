<?php
require_once __DIR__ . '/../core/Controller.php';

class EntradaController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        $entradaModel = $this->model('Entrada');
        $this->view('entradas/index', [
            'entradas' => $entradaModel->listarRecentes(20),
        ]);
    }

    public function nova(): void
    {
        $this->requireLogin();
        $fornecedores = $this->model('Fornecedor')->listarTodos();
        $produtos     = $this->model('Produto')->listarTodos();
        $this->view('entradas/form', [
            'fornecedores' => $fornecedores,
            'produtos'     => $produtos,
            'erro'         => null,
        ]);
    }

    public function confirmar(): void
    {
        $this->requireLogin();

        $fornecedorId = (int) ($_POST['fornecedor_id'] ?? 0);
        $produtosId   = $_POST['produto_id'] ?? [];
        $quantidades  = $_POST['quantidade'] ?? [];
        $custos       = $_POST['custo_unitario'] ?? [];

        $itens = [];
        foreach ($produtosId as $i => $produtoId) {
            $qtd = (int) ($quantidades[$i] ?? 0);
            if ($produtoId !== '' && $qtd > 0) {
                $itens[] = [
                    'produto_id'     => (int) $produtoId,
                    'quantidade'     => $qtd,
                    'custo_unitario' => (float) str_replace(',', '.', $custos[$i] ?? '0'),
                ];
            }
        }

        if (empty($itens) || $fornecedorId === 0) {
            $fornecedores = $this->model('Fornecedor')->listarTodos();
            $produtos     = $this->model('Produto')->listarTodos();
            $this->view('entradas/form', [
                'fornecedores' => $fornecedores,
                'produtos'     => $produtos,
                'erro'         => 'Selecione o fornecedor e ao menos um item com quantidade maior que zero.',
            ]);
            return;
        }

        $entradaModel = $this->model('Entrada');
        $entradaModel->registrar($fornecedorId, (int) $_SESSION['usuario_id'], $itens);

        $this->redirect('entrada/index');
    }
}
