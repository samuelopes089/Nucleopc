<?php
require_once __DIR__ . '/../core/Controller.php';

class RelatorioController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $pedidoModel  = $this->model('Pedido');
        $produtoModel = $this->model('Produto');
        $entradaModel = $this->model('Entrada');

        $this->view('relatorios/index', [
            'maisVendidos'      => $pedidoModel->produtosMaisVendidos(10),
            'vendasPorOrigem'   => $pedidoModel->totalVendasPorOrigem(),
            'fornecedores'      => $pedidoModel->fornecedoresResumo(),
            'entradasRecentes'  => $entradaModel->listarRecentes(10),
            'estoqueBaixo'      => $produtoModel->listarEstoqueBaixo(),
            'geradoEm'          => date('d/m/Y H:i'),
        ]);
    }

    /** Exporta o relatório atual em CSV (equivalente simplificado ao "Excel") */
    public function exportarCsv(): void
    {
        $this->requireLogin();
        $pedidoModel = $this->model('Pedido');

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=relatorio_nucleopc.csv');

        $saida = fopen('php://output', 'w');
        fputcsv($saida, ['Produto', 'Quantidade Vendida']);
        foreach ($pedidoModel->produtosMaisVendidos(50) as $linha) {
            fputcsv($saida, [$linha['nome'], $linha['qtde_vendida']]);
        }
        fclose($saida);
        exit;
    }
}
