<?php
$tituloPagina = 'Vendas';
$subtituloPagina = 'Vendas realizadas no balcão da loja física e pedidos online';
require __DIR__ . '/../layout/header.php';
?>

<div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
  <a href="index.php?url=venda/nova" class="btn btn-primary">+ Nova venda (balcão)</a>
</div>

<div class="card">
  <table>
    <thead>
      <tr><th>ID</th><th>Origem</th><th>Cliente / Vendedor</th><th>Data</th><th class="text-right">Valor total</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (empty($vendas)): ?>
        <tr><td colspan="6" class="text-muted">Nenhuma venda registrada.</td></tr>
      <?php else: foreach ($vendas as $v): ?>
        <tr>
          <td class="mono">#<?= $v['id'] ?></td>
          <td><span class="badge <?= $v['origem'] === 'online' ? 'badge-ok' : 'badge-off' ?>"><?= $v['origem'] === 'online' ? 'Online' : 'Balcão' ?></span></td>
          <td><?= htmlspecialchars($v['cliente_nome'] ?? $v['vendedor_nome'] ?? 'Cliente Balcão') ?></td>
          <td class="mono"><?= date('d/m/Y H:i', strtotime($v['data_pedido'])) ?></td>
          <td class="text-right mono">R$ <?= number_format($v['valor_total'], 2, ',', '.') ?></td>
          <td><a class="btn btn-ghost btn-sm" href="index.php?url=venda/notaFiscal/<?= $v['id'] ?>">Ver / Emitir NF</a></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
