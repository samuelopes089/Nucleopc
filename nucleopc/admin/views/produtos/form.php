<?php
$tituloPagina = !empty($produto['id']) ? 'Editar produto' : 'Novo produto';
$subtituloPagina = 'Cadastro de peças e componentes de informática';
require __DIR__ . '/../layout/header.php';
?>

<?php if ($erro): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

<div class="card" style="max-width:720px;">
  <form method="post" action="index.php?url=produto/salvar" enctype="multipart/form-data">
    <?php if (!empty($produto['id'])): ?>
      <input type="hidden" name="id" value="<?= $produto['id'] ?>">
    <?php endif; ?>
    <input type="hidden" name="imagem_atual" value="<?= htmlspecialchars($produto['imagem'] ?? 'sem-imagem.png') ?>">

    <div class="form-grid">
      <div class="form-row">
        <label for="sku">SKU (código)</label>
        <input type="text" id="sku" name="sku" value="<?= htmlspecialchars($produto['sku'] ?? '') ?>" required>
      </div>
      <div class="form-row">
        <label for="categoria_id">Categoria <a href="index.php?url=categoria/index" style="font-size:11px; font-weight:400;">(gerenciar)</a></label>
        <select id="categoria_id" name="categoria_id">
          <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['id'] ?>" <?= (isset($produto['categoria_id']) && (int)$produto['categoria_id'] === (int)$c['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-row">
      <label for="nome">Nome do produto</label>
      <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($produto['nome'] ?? '') ?>" required>
    </div>

    <div class="form-row">
      <label for="descricao">Descrição</label>
      <textarea id="descricao" name="descricao"><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
    </div>

    <div class="form-grid">
      <div class="form-row">
        <label for="marca">Marca</label>
        <input type="text" id="marca" name="marca" value="<?= htmlspecialchars($produto['marca'] ?? '') ?>">
      </div>
      <div class="form-row">
        <label for="imagem_arquivo">Foto do produto (JPG, PNG ou WEBP, até 2MB)</label>
        <input type="file" id="imagem_arquivo" name="imagem_arquivo" accept=".jpg,.jpeg,.png,.webp">
      </div>
    </div>

    <?php if (!empty($produto['imagem']) && $produto['imagem'] !== 'sem-imagem.png'): ?>
      <div class="form-row">
        <label>Imagem atual</label>
        <img src="<?= UPLOAD_URL . htmlspecialchars($produto['imagem']) ?>" alt="Imagem atual do produto"
             style="width:96px; height:96px; object-fit:cover; border-radius:6px; border:1px solid var(--border);"
             onerror="this.src='<?= UPLOAD_URL ?>sem-imagem.png'">
      </div>
    <?php endif; ?>

    <div class="form-grid">
      <div class="form-row">
        <label for="preco">Preço de venda (R$)</label>
        <input type="text" id="preco" name="preco" value="<?= htmlspecialchars((string)($produto['preco'] ?? '')) ?>" required>
      </div>
      <div class="form-row">
        <label for="custo">Custo (R$)</label>
        <input type="text" id="custo" name="custo" value="<?= htmlspecialchars((string)($produto['custo'] ?? '')) ?>">
      </div>
    </div>

    <?php if (empty($produto['id'])): ?>
    <div class="form-grid">
      <div class="form-row">
        <label for="estoque">Estoque inicial</label>
        <input type="number" id="estoque" name="estoque" value="0" min="0">
      </div>
      <div class="form-row">
        <label for="estoque_minimo">Estoque mínimo</label>
        <input type="number" id="estoque_minimo" name="estoque_minimo" value="3" min="0">
      </div>
    </div>
    <?php else: ?>
      <div class="form-row">
        <label for="estoque_minimo">Estoque mínimo (alerta)</label>
        <input type="number" id="estoque_minimo" name="estoque_minimo" value="<?= htmlspecialchars((string)($produto['estoque_minimo'] ?? 3)) ?>" min="0">
      </div>
      <p class="text-muted" style="font-size:12px;">O estoque atual (<?= $produto['estoque'] ?> un.) é ajustado por Entradas e Vendas.</p>
    <?php endif; ?>

    <div class="row-actions" style="margin-top:8px;">
      <button type="submit" class="btn btn-primary">Salvar</button>
      <a href="index.php?url=produto/index" class="btn btn-ghost">Cancelar</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
