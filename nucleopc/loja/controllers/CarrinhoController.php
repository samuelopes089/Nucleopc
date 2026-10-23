<?php
require_once __DIR__ . '/../core/Controller.php';

class CarrinhoController extends Controller
{
    public function index(): void
    {
        $carrinho = $this->model('Carrinho');
        $this->view('carrinho/index', [
            'itens' => $carrinho->itens(),
            'total' => $carrinho->total(),
        ]);
    }

    public function adicionar(): void
    {
        $produtoId  = (int) ($_POST['produto_id'] ?? 0);
        $quantidade = max(1, (int) ($_POST['quantidade'] ?? 1));

        $produtoModel = $this->model('Produto');
        $produto = $produtoModel->buscarPorId($produtoId);

        if ($produto) {
            $carrinho = $this->model('Carrinho');
            $carrinho->adicionar($produto, $quantidade);
        }

        $this->redirect('carrinho/index');
    }

    public function atualizar(): void
    {
        $carrinho = $this->model('Carrinho');
        $quantidades = $_POST['quantidade'] ?? [];
        foreach ($quantidades as $produtoId => $qtd) {
            $carrinho->atualizarQuantidade((int) $produtoId, (int) $qtd);
        }
        $this->redirect('carrinho/index');
    }

    public function remover(string $id): void
    {
        $carrinho = $this->model('Carrinho');
        $carrinho->remover((int) $id);
        $this->redirect('carrinho/index');
    }
}
