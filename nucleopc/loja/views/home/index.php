<?php
$tituloPagina = 'Núcleo PC — Loja de Computadores e Peças';
require __DIR__ . '/../layout/header.php';
?>

<section class="hero">
  <div class="container">
    <div class="hero-text">
      <div class="hero-eyebrow">MONTE, ATUALIZE, JOGUE</div>
      <h1>As peças certas para o PC que você quer montar.</h1>
      <p>Processadores, placas de vídeo, memórias e periféricos com estoque real e entrega para todo o Brasil.</p>
      <a href="index.php?url=produto/index" class="btn btn-accent">Ver catálogo completo</a>
    </div>
    <div class="hero-visual"></div>
  </div>
</section>

<div class="container">
  <section class="section">
    <div class="section-head">
      <h2>Categorias</h2>
    </div>
    <div class="product-grid" style="grid-template-columns:repeat(6,1fr);">
      <?php foreach ($categorias as $c): ?>
        <a href="index.php?url=produto/index&categoria=<?= $c['id'] ?>" class="product-card" style="text-decoration:none;">
          <div class="product-thumb"><?= htmlspecialchars($c['nome']) ?></div>
          <div class="product-info" style="text-align:center; padding:10px;">
            <span class="product-name" style="min-height:auto;"><?= htmlspecialchars($c['nome']) ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="section">
    <div class="section-head">
      <h2>Mais vendidos</h2>
      <a href="index.php?url=produto/index">Ver tudo</a>
    </div>
    <div class="product-grid">
      <?php foreach ($maisVendidos as $p): ?>
        <div class="product-card">
          <a href="index.php?url=produto/detalhe/<?= $p['id'] ?>">
            <div class="product-thumb">
              <img src="<?= UPLOAD_URL . htmlspecialchars($p['imagem'] ?: 'sem-imagem.png') ?>" alt="<?= htmlspecialchars($p['nome']) ?>"
                   onerror="this.src='<?= UPLOAD_URL ?>sem-imagem.png'">
            </div>
          </a>
          <div class="product-info">
            <span class="product-brand"><?= htmlspecialchars($p['marca']) ?></span>
            <a href="index.php?url=produto/detalhe/<?= $p['id'] ?>" class="product-name"><?= htmlspecialchars($p['nome']) ?></a>
            <div class="product-price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
          </div>
          <a href="index.php?url=produto/detalhe/<?= $p['id'] ?>" class="btn btn-dark btn-block">Ver produto</a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
