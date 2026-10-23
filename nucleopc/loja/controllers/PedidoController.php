<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../services/MercadoPagoService.php';

class PedidoController extends Controller
{
    public function checkout(): void
    {
        $this->requireCliente();

        $carrinho = $this->model('Carrinho');
        if (empty($carrinho->itens())) {
            $this->redirect('carrinho/index');
        }

        $clienteModel = $this->model('Cliente');
        $cliente = $clienteModel->buscarPorId((int) $_SESSION['cliente_id']);

        $this->view('pedido/checkout', [
            'itens'   => $carrinho->itens(),
            'total'   => $carrinho->total(),
            'cliente' => $cliente,
            'erro'    => null,
        ]);
    }

    /**
     * Cria o pedido (status "aguardando_pagamento"), gera a preferência
     * de pagamento no Mercado Pago e redireciona o cliente para o
     * Checkout Pro, onde ele escolhe Pix, cartão ou boleto de verdade.
     */
    public function finalizar(): void
    {
        $this->requireCliente();

        $carrinho = $this->model('Carrinho');
        $itens = $carrinho->itens();

        if (empty($itens)) {
            $this->redirect('carrinho/index');
        }

        $endereco = trim($_POST['endereco_entrega'] ?? '');
        $clienteModel = $this->model('Cliente');
        $cliente = $clienteModel->buscarPorId((int) $_SESSION['cliente_id']);

        if ($endereco === '') {
            $this->view('pedido/checkout', [
                'itens'   => $itens,
                'total'   => $carrinho->total(),
                'cliente' => $cliente,
                'erro'    => 'Informe o endereço de entrega.',
            ]);
            return;
        }

        $pedidoModel = $this->model('Pedido');
        try {
            $pedidoId = $pedidoModel->criarPedidoOnline((int) $_SESSION['cliente_id'], $itens, $endereco);

            $mp = new MercadoPagoService();
            $preferencia = $mp->criarPreferencia($pedidoId, array_values($itens), $cliente['email']);
            $pedidoModel->salvarPreferencia($pedidoId, $preferencia['id']);

            $carrinho->esvaziar();

            // init_point = checkout real (produção); sandbox_init_point = ambiente de teste.
            // Use sandbox_init_point enquanto estiver testando com credenciais TEST-.
            $urlPagamento = $preferencia['sandbox_init_point'] ?? $preferencia['init_point'];
            header('Location: ' . $urlPagamento);
            exit;
        } catch (Exception $e) {
            $this->view('pedido/checkout', [
                'itens'   => $itens,
                'total'   => $carrinho->total(),
                'cliente' => $cliente,
                'erro'    => 'Não foi possível iniciar o pagamento: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Página para onde o Mercado Pago redireciona o navegador do
     * cliente após o pagamento (aprovado, pendente ou recusado).
     * Esta página é só para exibição — quem efetivamente confirma o
     * pagamento no banco é o webhook(), chamado pelo Mercado Pago
     * diretamente do servidor deles (mais confiável que o retorno do navegador).
     */
    public function retorno(): void
    {
        $this->requireCliente();
        $pedidoId = (int) ($_GET['pedido'] ?? 0);
        $statusPagamento = $_GET['collection_status'] ?? $_GET['status'] ?? 'pending';
        $paymentId = $_GET['payment_id'] ?? $_GET['collection_id'] ?? null;

        $pedidoModel = $this->model('Pedido');
        $pedido = $pedidoModel->buscarPorId($pedidoId);

        if (!$pedido || (int) $pedido['cliente_id'] !== (int) $_SESSION['cliente_id']) {
            $this->redirect('home/index');
        }

        // Atualização otimista a partir do retorno do navegador. O webhook (fonte
        // confiável) fará/confirmará a mesma atualização de forma assíncrona.
        if ($pedido['status'] === 'aguardando_pagamento') {
            // Converte o retorno do pagamento para o status usado pela loja.
            if ($statusPagamento === 'approved') {
                $novoStatus = 'confirmado';
            } elseif ($statusPagamento === 'rejected') {
                $novoStatus = 'cancelado';
            } else {
                $novoStatus = 'aguardando_pagamento';
            }
            if ($novoStatus !== 'aguardando_pagamento') {
                $pedidoModel->atualizarStatusPagamento($pedidoId, $novoStatus, $paymentId, $statusPagamento);
                $pedido = $pedidoModel->buscarPorId($pedidoId);
            }
        }

        $this->view('pedido/retorno', [
            'pedido'          => $pedido,
            'itens'           => $pedidoModel->itensDoPedido($pedidoId),
            'statusPagamento' => $statusPagamento,
        ]);
    }

    /**
     * Endpoint chamado pelo servidor do Mercado Pago (notification_url)
     * quando o status de um pagamento muda. Não depende do navegador do
     * cliente estar aberto — é a forma confiável de confirmar a venda.
     * Requer uma URL pública (HTTPS) configurada em MP_BASE_URL.
     */
    public function webhook(): void
    {
        $topico = $_GET['topic'] ?? $_GET['type'] ?? null;
        $pagamentoId = $_GET['id'] ?? $_GET['data_id'] ?? null;

        header('Content-Type: application/json');

        if ($topico !== 'payment' || !$pagamentoId) {
            http_response_code(200); // outros tópicos são apenas confirmados, sem ação
            echo json_encode(['ok' => true]);
            return;
        }

        try {
            $mp = new MercadoPagoService();
            $pagamento = $mp->consultarPagamento((string) $pagamentoId);

            $pedidoId = (int) ($pagamento['external_reference'] ?? 0);
            $statusMp = $pagamento['status'] ?? 'pending'; // approved | rejected | pending | in_process | cancelled

            if ($pedidoId > 0) {
                // Usa o status informado pelo Mercado Pago para atualizar o pedido.
                if ($statusMp === 'approved') {
                    $novoStatus = 'confirmado';
                } elseif ($statusMp === 'rejected' || $statusMp === 'cancelled') {
                    $novoStatus = 'cancelado';
                } else {
                    $novoStatus = 'aguardando_pagamento';
                }

                $pedidoModel = $this->model('Pedido');
                $pedidoModel->atualizarStatusPagamento($pedidoId, $novoStatus, (string) $pagamentoId, $statusMp);
            }

            http_response_code(200);
            echo json_encode(['ok' => true]);
        } catch (Exception $e) {
            http_response_code(200); // sempre 200 para o MP não ficar reenviando indefinidamente
            echo json_encode(['ok' => false, 'erro' => $e->getMessage()]);
        }
    }

    public function sucesso(string $id): void
    {
        $this->requireCliente();
        $pedidoModel = $this->model('Pedido');

        $pedido = $pedidoModel->buscarPorId((int) $id);
        if (!$pedido || (int) $pedido['cliente_id'] !== (int) $_SESSION['cliente_id']) {
            $this->redirect('home/index');
        }

        $this->view('pedido/sucesso', [
            'pedido' => $pedido,
            'itens'  => $pedidoModel->itensDoPedido((int) $id),
        ]);
    }
}
