<?php
$tituloPagina = 'Produtos';
$subtituloPagina = 'Catálogo de peças e componentes vendidos na loja';
require __DIR__ . '/../layout/header.php';
?>

<?php if ($mensagem): ?>
  <div class="alert alert-success">Produto <?= htmlspecialchars($mensagem) ?> com sucesso.</div>
<?php endif; ?>

<div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
  <a href="index.php?url=produto/novo" class="btn btn-primary">+ Novo produto</a>
</div>

<div class="card">
  <table>
    <thead>
      <tr><th></th><th>SKU</th><th>Produto</th><th>Categoria</th><th class="text-right">Preço</th><th class="text-right">Estoque</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($produtos as $p): ?>
        <tr>
          <td>
            <img src="<?= UPLOAD_URL . htmlspecialchars($p['imagem'] ?: 'sem-imagem.png') ?>" alt=""
                 style="width:34px; height:34px; object-fit:cover; border-radius:4px; border:1px solid var(--border); vertical-align:middle;"
                 onerror="this.src='<?= UPLOAD_URL ?>sem-imagem.png'">
          </td>
          <td class="mono"><?= htmlspecialchars($p['sku']) ?></td>
          <td><?= htmlspecialchars($p['nome']) ?></td>
          <td class="text-muted"><?= htmlspecialchars($p['categoria_nome'] ?? '—') ?></td>
          <td class="text-right mono">R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
          <td class="text-right">
            <span class="badge <?= $p['estoque'] <= $p['estoque_minimo'] ? 'badge-warn' : 'badge-ok' ?>"><?= $p['estoque'] ?></span>
          </td>
          <td><span class="badge <?= $p['ativo'] ? 'badge-ok' : 'badge-off' ?>"><?= $p['ativo'] ? 'ativo' : 'inativo' ?></span></td>
          <td>
            <div class="row-actions">
              <a class="btn btn-ghost btn-sm" href="index.php?url=produto/editar/<?= $p['id'] ?>">Editar</a>
              <?php if ($p['ativo']): ?>
                <a class="btn btn-danger btn-sm" href="index.php?url=produto/inativar/<?= $p['id'] ?>">Inativar</a>
              <?php else: ?>
                <a class="btn btn-ghost btn-sm" href="index.php?url=produto/ativar/<?= $p['id'] ?>">Ativar</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
