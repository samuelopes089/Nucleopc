<?php
require_once __DIR__ . '/../core/Controller.php';

class VendaController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        $pedidoModel = $this->model('Pedido');
        $this->view('vendas/index', [
            'vendas' => $pedidoModel->listarRecentes(20),
        ]);
    }

    public function nova(): void
    {
        $this->requireLogin();
        $produtos = $this->model('Produto')->listarTodos();
        $this->view('vendas/form', ['produtos' => $produtos, 'erro' => null]);
    }

    public function finalizar(): void
    {
        $this->requireLogin();

        $produtosId  = $_POST['produto_id'] ?? [];
        $quantidades = $_POST['quantidade'] ?? [];
        $precos      = $_POST['preco_unitario'] ?? [];

        $itens = [];
        foreach ($produtosId as $i => $produtoId) {
            $qtd = (int) ($quantidades[$i] ?? 0);
            if ($produtoId !== '' && $qtd > 0) {
                $itens[] = [
                    'produto_id'     => (int) $produtoId,
                    'quantidade'     => $qtd,
                    'preco_unitario' => (float) str_replace(',', '.', $precos[$i] ?? '0'),
                ];
            }
        }

        if (empty($itens)) {
            $produtos = $this->model('Produto')->listarTodos();
            $this->view('vendas/form', ['produtos' => $produtos, 'erro' => 'Adicione ao menos um item com quantidade maior que zero.']);
            return;
        }

        $pedidoModel = $this->model('Pedido');
        try {
            $pedidoId = $pedidoModel->registrarVendaBalcao(null, (int) $_SESSION['usuario_id'], $itens);
            $this->redirect('venda/index?msg=venda_' . $pedidoId);
        } catch (Exception $e) {
            $produtos = $this->model('Produto')->listarTodos();
            $this->view('vendas/form', ['produtos' => $produtos, 'erro' => $e->getMessage()]);
        }
    }

    public function notaFiscal(string $id): void
    {
        $this->requireLogin();
        $pedidoModel = $this->model('Pedido');

        $pedido = $pedidoModel->buscarPorId((int) $id);
        if (!$pedido) {
            $this->redirect('venda/index');
        }

        $nota  = $pedidoModel->emitirNotaFiscal((int) $id);
        $itens = $pedidoModel->itensDoPedido((int) $id);

        $this->view('vendas/nota_fiscal', [
            'pedido' => $pedido,
            'nota'   => $nota,
            'itens'  => $itens,
        ]);
    }
}
