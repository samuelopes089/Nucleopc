<?php
$tituloPagina = 'Status do pagamento — Núcleo PC';
require __DIR__ . '/../layout/header.php';

$status = $pedido['status'];

// Define a mensagem que aparece conforme o status do pedido.
if ($status === 'confirmado') {
    $config = [
        'icone' => '✅',
        'titulo' => 'Pagamento aprovado!',
        'texto' => 'Seu pedido #' . $pedido['id'] . ' foi confirmado e já está sendo preparado para envio.',
        'classe' => 'alert-success',
    ];
} elseif ($status === 'aguardando_pagamento') {
    $config = [
        'icone' => '⏳',
        'titulo' => 'Pagamento em processamento',
        'texto' => 'Recebemos seu pedido #' . $pedido['id'] . ', mas o pagamento ainda está sendo processado pelo Mercado Pago (comum em Pix recém-gerado ou boleto). Assim que for aprovado, você verá a confirmação em "Meus pedidos".',
        'classe' => 'alert-warn',
    ];
} elseif ($status === 'cancelado') {
    $config = [
        'icone' => '❌',
        'titulo' => 'Pagamento não aprovado',
        'texto' => 'O pagamento do pedido #' . $pedido['id'] . ' não foi aprovado. Os itens foram devolvidos ao estoque e nenhuma cobrança foi confirmada. Você pode tentar novamente.',
        'classe' => 'alert-error',
    ];
} else {
    $config = [
        'icone' => 'ℹ️',
        'titulo' => 'Status do pedido',
        'texto' => 'Status atual: ' . htmlspecialchars($status),
        'classe' => 'alert-warn',
    ];
}
?>

<div class="container">
  <section class="section">
    <div class="form-card wide" style="text-align:center;">
      <div style="font-size:40px; margin-bottom:10px;"><?= $config['icone'] ?></div>
      <h1><?= htmlspecialchars($config['titulo']) ?></h1>
      <div class="alert <?= $config['classe'] ?>" style="text-align:left;"><?= htmlspecialchars($config['texto']) ?></div>

      <table class="cart-table" style="text-align:left; margin-top:18px;">
        <thead><tr><th>Produto</th><th>Qtd</th><th>Subtotal</th></tr></thead>
        <tbody>
          <?php foreach ($itens as $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['nome']) ?></td>
              <td><?= $item['quantidade'] ?></td>
              <td>R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="total-row" style="justify-content:flex-end; gap:10px;">
        <span>Total do pedido:</span>
        <strong>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></strong>
      </div>

      <div style="display:flex; gap:10px; justify-content:center; margin-top:16px;">
        <?php if ($status === 'cancelado'): ?>
          <a href="index.php?url=carrinho/index" class="btn btn-accent">Tentar novamente</a>
        <?php endif; ?>
        <a href="index.php?url=cliente/meusPedidos" class="btn btn-ghost">Ver meus pedidos</a>
        <a href="index.php?url=produto/index" class="btn btn-ghost">Continuar comprando</a>
      </div>
    </div>
  </section>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
