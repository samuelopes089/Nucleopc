<?php
require_once __DIR__ . '/../core/Controller.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $produtoModel = $this->model('Produto');
        $pedidoModel  = $this->model('Pedido');
        $entradaModel = $this->model('Entrada');

        $data = [
            'maisVendidos'   => $pedidoModel->produtosMaisVendidos(5),
            'estoqueBaixo'   => $produtoModel->listarEstoqueBaixo(),
            'entradasRecentes' => $entradaModel->listarRecentes(5),
            'vendasRecentes' => $pedidoModel->listarRecentes(5),
        ];

        $this->view('dashboard/index', $data);
    }
}
