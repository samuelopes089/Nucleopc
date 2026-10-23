<?php
$tituloPagina = 'Nova venda (balcão)';
$subtituloPagina = 'Selecione os produtos vendidos; a disponibilidade em estoque será conferida';
require __DIR__ . '/../layout/header.php';
?>

<?php if ($erro): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

<div class="card" style="max-width:820px;">
  <form method="post" action="index.php?url=venda/finalizar">
    <label style="font-size:12.5px; color:var(--muted); display:block; margin-bottom:8px;">Itens vendidos</label>
    <div class="item-line-head">
      <span>Produto (SKU)</span><span>Quantidade</span><span>Preço unitário (R$)</span>
    </div>

    <div id="itens-container">
      <?php for ($i = 0; $i < 5; $i++): ?>
        <div class="item-line">
          <select name="produto_id[]" class="produto-input" onchange="preencherPreco(this)">
            <option value="">— não usar esta linha —</option>
            <?php foreach ($produtos as $p): ?>
              <option value="<?= $p['id'] ?>" data-preco="<?= $p['preco'] ?>"><?= htmlspecialchars($p['sku'] . ' — ' . $p['nome']) ?> (estoque: <?= $p['estoque'] ?>)</option>
            <?php endforeach; ?>
          </select>
          <input type="number" name="quantidade[]" class="qtd-input" min="0" value="0" oninput="recalcularTotal('itens-container','total-venda')">
          <input type="text" name="preco_unitario[]" class="preco-input" value="0,00" oninput="recalcularTotal('itens-container','total-venda')">
        </div>
      <?php endfor; ?>
    </div>

    <p class="text-muted" style="font-size:12px;">Dica: o preço é sugerido a partir do cadastro do produto, mas pode ser ajustado manualmente.</p>

    <div class="total-line">
      <span class="text-muted">Total da venda:</span>
      <span class="value" id="total-venda">R$ 0,00</span>
    </div>

    <div class="row-actions" style="margin-top:16px;">
      <button type="submit" class="btn btn-primary">Finalizar venda</button>
      <a href="index.php?url=venda/index" class="btn btn-ghost">Cancelar</a>
    </div>
  </form>
</div>

<script>
  function preencherPreco(select) {
    const linha = select.closest('.item-line');
    const precoInput = linha.querySelector('.preco-input');
    const opcao = select.selectedOptions[0];
    if (opcao && opcao.dataset.preco) {
      precoInput.value = parseFloat(opcao.dataset.preco).toFixed(2).replace('.', ',');
      recalcularTotal('itens-container', 'total-venda');
    }
  }
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
