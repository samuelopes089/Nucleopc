<?php
require_once __DIR__ . '/../core/Controller.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $categoriaModel = $this->model('Categoria');
        $produtoModel   = $this->model('Produto');

        $this->view('home/index', [
            'categorias'    => $categoriaModel->listarTodas(),
            'maisVendidos'  => $produtoModel->maisVendidos(8),
        ]);
    }
}
