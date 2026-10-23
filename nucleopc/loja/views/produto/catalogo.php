<?php
$tituloPagina = 'Catálogo — Núcleo PC';
require __DIR__ . '/../layout/header.php';
?>

<div class="container">
  <section class="section">
    <div class="breadcrumb"><a href="index.php?url=home/index">Início</a> / Catálogo</div>

    <div class="catalog-layout">
      <aside class="filters">
        <h3>Categorias</h3>
        <ul>
          <li><a href="index.php?url=produto/index" class="<?= !$categoriaAtual ? 'active' : '' ?>">Todos os produtos</a></li>
          <?php foreach ($categorias as $c): ?>
            <li><a href="index.php?url=produto/index&categoria=<?= $c['id'] ?>" class="<?= $categoriaAtual == $c['id'] ? 'active' : '' ?>"><?= htmlspecialchars($c['nome']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </aside>

      <div>
        <div class="toolbar">
          <span class="text-muted" style="color:var(--muted); font-size:13px;">
            <?= count($produtos) ?> produto(s) encontrado(s)<?= $buscaAtual ? ' para "' . htmlspecialchars($buscaAtual) . '"' : '' ?>
          </span>
          <form method="get" action="index.php">
            <input type="hidden" name="url" value="produto/index">
            <?php if ($categoriaAtual): ?><input type="hidden" name="categoria" value="<?= $categoriaAtual ?>"><?php endif; ?>
            <?php if ($buscaAtual): ?><input type="hidden" name="q" value="<?= htmlspecialchars($buscaAtual) ?>"><?php endif; ?>
            <select name="ordem" onchange="this.form.submit()">
              <option value="relevancia" <?= $ordemAtual === 'relevancia' ? 'selected' : '' ?>>Relevância</option>
              <option value="menor_preco" <?= $ordemAtual === 'menor_preco' ? 'selected' : '' ?>>Menor preço</option>
              <option value="maior_preco" <?= $ordemAtual === 'maior_preco' ? 'selected' : '' ?>>Maior preço</option>
            </select>
          </form>
        </div>

        <div class="product-grid">
          <?php if (empty($produtos)): ?>
            <p style="color:var(--muted);">Nenhum produto encontrado com esses filtros.</p>
          <?php else: foreach ($produtos as $p): ?>
            <div class="product-card">
              <a href="index.php?url=produto/detalhe/<?= $p['id'] ?>">
                <div class="product-thumb">
                  <img src="<?= UPLOAD_URL . htmlspecialchars($p['imagem'] ?: 'sem-imagem.png') ?>" alt="<?= htmlspecialchars($p['nome']) ?>"
                       onerror="this.src='<?= UPLOAD_URL ?>sem-imagem.png'">
                </div>
              </a>
              <div class="product-info">
                <span class="product-brand"><?= htmlspecialchars($p['marca']) ?> · <?= htmlspecialchars($p['categoria_nome']) ?></span>
                <a href="index.php?url=produto/detalhe/<?= $p['id'] ?>" class="product-name"><?= htmlspecialchars($p['nome']) ?></a>
                <span class="product-stock"><?= $p['estoque'] ?> em estoque</span>
                <div class="product-price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
              </div>
              <a href="index.php?url=produto/detalhe/<?= $p['id'] ?>" class="btn btn-dark btn-block">Ver produto</a>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
  </section>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
