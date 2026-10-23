<?php
$tituloPagina = 'Registrar entrada de mercadoria';
$subtituloPagina = 'Informe o que chegou para atualizar as quantidades disponíveis';
require __DIR__ . '/../layout/header.php';
?>

<?php if ($erro): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

<div class="card" style="max-width:820px;">
  <form method="post" action="index.php?url=entrada/confirmar" id="form-entrada">
    <div class="form-row">
      <label for="fornecedor_id">Fornecedor</label>
      <select id="fornecedor_id" name="fornecedor_id" required>
        <option value="">Selecione...</option>
        <?php foreach ($fornecedores as $f): ?>
          <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <label style="font-size:12.5px; color:var(--muted); display:block; margin: 18px 0 8px;">Itens da entrada</label>
    <div class="item-line-head">
      <span>Produto (SKU)</span><span>Quantidade</span><span>Custo unitário (R$)</span>
    </div>

    <div id="itens-container">
      <?php for ($i = 0; $i < 5; $i++): ?>
        <div class="item-line">
          <select name="produto_id[]" class="produto-input">
            <option value="">— não usar esta linha —</option>
            <?php foreach ($produtos as $p): ?>
              <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['sku'] . ' — ' . $p['nome']) ?></option>
            <?php endforeach; ?>
          </select>
          <input type="number" name="quantidade[]" class="qtd-input" min="0" value="0" oninput="recalcularTotal('itens-container','total-entrada')">
          <input type="text" name="custo_unitario[]" class="preco-input" value="0,00" oninput="recalcularTotal('itens-container','total-entrada')">
        </div>
      <?php endfor; ?>
    </div>

    <div class="total-line">
      <span class="text-muted">Total da entrada:</span>
      <span class="value" id="total-entrada">R$ 0,00</span>
    </div>

    <div class="row-actions" style="margin-top:16px;">
      <button type="submit" class="btn btn-primary">Confirmar entrada</button>
      <a href="index.php?url=entrada/index" class="btn btn-ghost">Cancelar</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
