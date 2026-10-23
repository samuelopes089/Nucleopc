<?php
$tituloPagina = 'Relatórios';
$subtituloPagina = 'Acompanhe os resultados da loja física e online';
require __DIR__ . '/../layout/header.php';
?>

<div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
  <a href="index.php?url=relatorio/exportarCsv" class="btn btn-ghost">Exportar CSV (Excel)</a>
  <a href="javascript:window.print()" class="btn btn-primary" style="margin-left:8px;">Exportar PDF (imprimir)</a>
</div>

<p class="text-muted" style="font-size:12px; margin-top:-6px;">Gerado em: <?= $geradoEm ?></p>

<div class="grid grid-2">
  <div class="card">
    <h2>Produtos mais vendidos</h2>
    <table>
      <thead><tr><th>Produto</th><th class="text-right">Qtde. vendida</th></tr></thead>
      <tbody>
        <?php foreach ($maisVendidos as $p): ?>
          <tr><td><?= htmlspecialchars($p['nome']) ?></td><td class="text-right mono"><?= $p['qtde_vendida'] ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h2>Vendas por origem</h2>
    <table>
      <thead><tr><th>Origem</th><th class="text-right">Pedidos</th><th class="text-right">Valor total</th></tr></thead>
      <tbody>
        <?php foreach ($vendasPorOrigem as $v): ?>
          <tr>
            <td><?= $v['origem'] === 'online' ? 'Loja Online' : 'Loja Física' ?></td>
            <td class="text-right mono"><?= $v['total_pedidos'] ?></td>
            <td class="text-right mono">R$ <?= number_format($v['valor_total'], 2, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h2>Fornecedores</h2>
    <table>
      <thead><tr><th>Fornecedor</th><th class="text-right">Entradas</th><th class="text-right">Valor total</th></tr></thead>
      <tbody>
        <?php foreach ($fornecedores as $f): ?>
          <tr>
            <td><?= htmlspecialchars($f['nome']) ?></td>
            <td class="text-right mono"><?= $f['entradas'] ?></td>
            <td class="text-right mono">R$ <?= number_format($f['valor_total'], 2, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h2>Estoque baixo <small>atenção</small></h2>
    <table>
      <thead><tr><th>Produto</th><th>SKU</th><th class="text-right">Qtde.</th><th class="text-right">Mínimo</th></tr></thead>
      <tbody>
        <?php if (empty($estoqueBaixo)): ?>
          <tr><td colspan="4" class="text-muted">Nenhum item abaixo do estoque mínimo.</td></tr>
        <?php else: foreach ($estoqueBaixo as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <td class="mono"><?= htmlspecialchars($p['sku']) ?></td>
            <td class="text-right"><span class="badge badge-warn"><?= $p['estoque'] ?></span></td>
            <td class="text-right text-muted"><?= $p['estoque_minimo'] ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
