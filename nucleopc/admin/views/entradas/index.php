<?php
$tituloPagina = 'Entradas de estoque';
$subtituloPagina = 'Recebimento de mercadoria dos fornecedores';
require __DIR__ . '/../layout/header.php';
?>

<div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
  <a href="index.php?url=entrada/nova" class="btn btn-primary">+ Nova entrada</a>
</div>

<div class="card">
  <table>
    <thead>
      <tr><th>ID</th><th>Fornecedor</th><th>Data</th><th>Status</th><th class="text-right">Valor total</th></tr>
    </thead>
    <tbody>
      <?php if (empty($entradas)): ?>
        <tr><td colspan="5" class="text-muted">Nenhuma entrada registrada.</td></tr>
      <?php else: foreach ($entradas as $e): ?>
        <tr>
          <td class="mono">#<?= $e['id'] ?></td>
          <td><?= htmlspecialchars($e['fornecedor_nome']) ?></td>
          <td class="mono"><?= $e['data_entrada'] ?></td>
          <td><span class="badge badge-ok"><?= $e['status'] ?></span></td>
          <td class="text-right mono">R$ <?= number_format($e['valor_total'], 2, ',', '.') ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
