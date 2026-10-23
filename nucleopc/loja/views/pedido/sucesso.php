<?php
$tituloPagina = 'Pedido confirmado — Núcleo PC';
require __DIR__ . '/../layout/header.php';
?>

<div class="container">
  <section class="section">
    <div class="form-card wide" style="text-align:center;">
      <div style="font-size:40px; margin-bottom:10px;">✅</div>
      <h1>Pedido #<?= $pedido['id'] ?> confirmado!</h1>
      <p class="sub">Obrigado por comprar na Núcleo PC. Você pode acompanhar o status em "Meus pedidos".</p>

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
        <span>Total pago:</span>
        <strong>R$ <?= number_format($pedido['valor_total'], 2, ',', '.') ?></strong>
      </div>

      <a href="index.php?url=produto/index" class="btn btn-accent" style="margin-top:10px;">Continuar comprando</a>
    </div>
  </section>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
