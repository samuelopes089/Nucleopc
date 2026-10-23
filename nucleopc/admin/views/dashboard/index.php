<?php
$tituloPagina = 'Dashboard';
$subtituloPagina = 'Visão geral da loja física e online';
require __DIR__ . '/../layout/header.php';
?>

<div class="grid grid-3" style="margin-bottom:18px;">
  <div class="card">
    <h2>Estoque baixo</h2>
    <div class="metric" style="color: var(--warn);"><?= count($estoqueBaixo) ?></div>
    <div class="metric-label">produtos abaixo do mínimo</div>
  </div>
  <div class="card">
    <h2>Vendas recentes</h2>
    <div class="metric"><?= count($vendasRecentes) ?></div>
    <div class="metric-label">últimos pedidos registrados</div>
  </div>
  <div class="card">
    <h2>Entradas recentes</h2>
    <div class="metric"><?= count($entradasRecentes) ?></div>
    <div class="metric-label">recebimentos de fornecedores</div>
  </div>
</div>

<div class="grid grid-2">
  <div class="card">
    <h2>Produtos mais vendidos</h2>
    <table>
      <thead><tr><th>Produto</th><th class="text-right">Qtde. vendida</th></tr></thead>
      <tbody>
        <?php if (empty($maisVendidos)): ?>
          <tr><td colspan="2" class="text-muted">Nenhuma venda registrada ainda.</td></tr>
        <?php else: foreach ($maisVendidos as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <td class="text-right mono"><?= $p['qtde_vendida'] ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h2>Estoque baixo <small>atenção</small></h2>
    <table>
      <thead><tr><th>Produto</th><th>SKU</th><th class="text-right">Qtde.</th></tr></thead>
      <tbody>
        <?php if (empty($estoqueBaixo)): ?>
          <tr><td colspan="3" class="text-muted">Nenhum item abaixo do estoque mínimo.</td></tr>
        <?php else: foreach ($estoqueBaixo as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <td class="mono"><?= htmlspecialchars($p['sku']) ?></td>
            <td class="text-right"><span class="badge badge-warn"><?= $p['estoque'] ?></span></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h2>Entradas recentes</h2>
    <table>
      <thead><tr><th>ID</th><th>Fornecedor</th><th>Data</th><th class="text-right">Valor</th></tr></thead>
      <tbody>
        <?php foreach ($entradasRecentes as $e): ?>
          <tr>
            <td class="mono">#<?= $e['id'] ?></td>
            <td><?= htmlspecialchars($e['fornecedor_nome']) ?></td>
            <td class="mono"><?= $e['data_entrada'] ?></td>
            <td class="text-right mono">R$ <?= number_format($e['valor_total'], 2, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h2>Vendas recentes</h2>
    <table>
      <thead><tr><th>ID</th><th>Origem</th><th>Cliente/Vendedor</th><th class="text-right">Valor</th></tr></thead>
      <tbody>
        <?php foreach ($vendasRecentes as $v): ?>
          <tr>
            <td class="mono">#<?= $v['id'] ?></td>
            <td><span class="badge <?= $v['origem'] === 'online' ? 'badge-ok' : 'badge-off' ?>"><?= $v['origem'] === 'online' ? 'Online' : 'Balcão' ?></span></td>
            <td><?= htmlspecialchars($v['cliente_nome'] ?? $v['vendedor_nome'] ?? 'Cliente Balcão') ?></td>
            <td class="text-right mono">R$ <?= number_format($v['valor_total'], 2, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
