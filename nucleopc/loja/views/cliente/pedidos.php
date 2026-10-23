<?php
$tituloPagina = 'Meus pedidos — Núcleo PC';
require __DIR__ . '/../layout/header.php';
?>

<div class="container">
  <section class="section">
    <div class="section-head"><h2>Meus pedidos</h2></div>

    <?php if (empty($pedidos)): ?>
      <p style="color:var(--muted);">Você ainda não fez nenhum pedido. <a href="index.php?url=produto/index" style="color:var(--accent); font-weight:600;">Ver catálogo</a></p>
    <?php else: ?>
      <table class="cart-table">
        <thead><tr><th>Pedido</th><th>Data</th><th>Status</th><th>Total</th></tr></thead>
        <tbody>
          <?php foreach ($pedidos as $p): ?>
            <tr>
              <td>#<?= $p['id'] ?></td>
              <td><?= date('d/m/Y H:i', strtotime($p['data_pedido'])) ?></td>
              <td><?= ucfirst(str_replace('_', ' ', $p['status'])) ?></td>
              <td>R$ <?= number_format($p['valor_total'], 2, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
