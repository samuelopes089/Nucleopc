<?php
require_once __DIR__ . '/../core/Controller.php';

class ProdutoController extends Controller
{
    /** Catálogo com filtros de categoria, busca e ordenação */
    public function index(): void
    {
        $categoriaId = isset($_GET['categoria']) ? (int) $_GET['categoria'] : null;
        $busca       = isset($_GET['q']) ? trim($_GET['q']) : null;
        $ordem       = $_GET['ordem'] ?? 'relevancia';

        $produtoModel   = $this->model('Produto');
        $categoriaModel = $this->model('Categoria');

        $this->view('produto/catalogo', [
            'produtos'        => $produtoModel->listarCatalogo($categoriaId, $busca, $ordem),
            'categorias'      => $categoriaModel->listarTodas(),
            'categoriaAtual'  => $categoriaId,
            'buscaAtual'      => $busca,
            'ordemAtual'      => $ordem,
        ]);
    }

    public function detalhe(string $id): void
    {
        $produtoModel = $this->model('Produto');
        $produto = $produtoModel->buscarPorId((int) $id);

        if (!$produto) {
            $this->redirect('produto/index');
        }

        $relacionados = $produtoModel->relacionados((int) $produto['categoria_id'], (int) $produto['id']);

        $this->view('produto/detalhe', [
            'produto'      => $produto,
            'relacionados' => $relacionados,
        ]);
    }
}
