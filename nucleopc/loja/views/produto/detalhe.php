<?php
$tituloPagina = $produto['nome'] . ' — Núcleo PC';
require __DIR__ . '/../layout/header.php';
?>

<div class="container">
  <section class="section">
    <div class="breadcrumb">
      <a href="index.php?url=home/index">Início</a> /
      <a href="index.php?url=produto/index&categoria=<?= $produto['categoria_id'] ?>"><?= htmlspecialchars($produto['categoria_nome']) ?></a> /
      <?= htmlspecialchars($produto['nome']) ?>
    </div>

    <div class="product-detail">
      <div class="product-detail-thumb">
        <img src="<?= UPLOAD_URL . htmlspecialchars($produto['imagem'] ?: 'sem-imagem.png') ?>" alt="<?= htmlspecialchars($produto['nome']) ?>"
             onerror="this.src='<?= UPLOAD_URL ?>sem-imagem.png'">
      </div>

      <div>
        <span class="product-brand"><?= htmlspecialchars($produto['marca']) ?></span>
        <h1><?= htmlspecialchars($produto['nome']) ?></h1>
        <span class="product-stock"><?= $produto['estoque'] ?> unidade(s) em estoque · SKU <?= htmlspecialchars($produto['sku']) ?></span>

        <div class="price-box">
          <div class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
          <div class="installments">em até 10x no cartão, ou com desconto no PIX</div>

          <form class="qty-form" action="index.php?url=carrinho/adicionar" method="post">
            <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
            <input type="number" name="quantidade" value="1" min="1" max="<?= $produto['estoque'] ?>">
            <button type="submit" class="btn btn-accent">Adicionar ao carrinho</button>
          </form>
        </div>

        <div class="product-desc"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></div>
      </div>
    </div>

    <?php if (!empty($relacionados)): ?>
      <div class="section-head">
        <h2>Você também pode gostar</h2>
      </div>
      <div class="product-grid">
        <?php foreach ($relacionados as $r): ?>
          <div class="product-card">
            <a href="index.php?url=produto/detalhe/<?= $r['id'] ?>">
              <div class="product-thumb">
                <img src="<?= UPLOAD_URL . htmlspecialchars($r['imagem'] ?: 'sem-imagem.png') ?>" alt="<?= htmlspecialchars($r['nome']) ?>"
                     onerror="this.src='<?= UPLOAD_URL ?>sem-imagem.png'">
              </div>
            </a>
            <div class="product-info">
              <span class="product-brand"><?= htmlspecialchars($r['marca']) ?></span>
              <a href="index.php?url=produto/detalhe/<?= $r['id'] ?>" class="product-name"><?= htmlspecialchars($r['nome']) ?></a>
              <div class="product-price">R$ <?= number_format($r['preco'], 2, ',', '.') ?></div>
            </div>
            <a href="index.php?url=produto/detalhe/<?= $r['id'] ?>" class="btn btn-dark btn-block">Ver produto</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
