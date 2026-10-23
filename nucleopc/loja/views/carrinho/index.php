<?php
$tituloPagina = 'Carrinho — Núcleo PC';
require __DIR__ . '/../layout/header.php';
?>

<div class="container">
  <section class="section">
    <div class="section-head"><h2>Meu carrinho</h2></div>

    <?php if (empty($itens)): ?>
      <p style="color:var(--muted);">Seu carrinho está vazio. <a href="index.php?url=produto/index" style="color:var(--accent); font-weight:600;">Ver catálogo</a></p>
    <?php else: ?>
      <div class="cart-layout">
        <form method="post" action="index.php?url=carrinho/atualizar">
          <table class="cart-table">
            <thead>
              <tr><th>Produto</th><th>Preço</th><th>Quantidade</th><th>Subtotal</th><th></th></tr>
            </thead>
            <tbody>
              <?php foreach ($itens as $item): ?>
                <tr>
                  <td>
                    <div class="cart-product">
                      <div class="cart-thumb">
                        <img src="<?= UPLOAD_URL . htmlspecialchars($item['imagem'] ?: 'sem-imagem.png') ?>" alt=""
                             onerror="this.src='<?= UPLOAD_URL ?>sem-imagem.png'">
                      </div>
                      <div>
                        <div style="font-weight:600;"><?= htmlspecialchars($item['nome']) ?></div>
                        <div style="font-size:11.5px; color:var(--muted);">SKU <?= htmlspecialchars($item['sku']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                  <td><input type="number" name="quantidade[<?= $item['produto_id'] ?>]" value="<?= $item['quantidade'] ?>" min="1" style="width:70px;"></td>
                  <td>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></td>
                  <td><a href="index.php?url=carrinho/remover/<?= $item['produto_id'] ?>" style="color:#C4341A; font-size:12.5px;">Remover</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <button type="submit" class="btn btn-outline" style="background:#fff; border:1px solid var(--border); color:var(--text); margin-top:14px;">Atualizar quantidades</button>
        </form>

        <aside class="cart-summary">
          <h3 style="margin-top:0; font-size:14px;">Resumo do pedido</h3>
          <div class="total-row">
            <span>Total</span>
            <span>R$ <?= number_format($total, 2, ',', '.') ?></span>
          </div>
          <a href="index.php?url=pedido/checkout" class="btn btn-accent btn-block">Finalizar compra</a>
        </aside>
      </div>
    <?php endif; ?>
  </section>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
