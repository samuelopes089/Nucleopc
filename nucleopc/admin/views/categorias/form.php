<?php
$tituloPagina = !empty($categoria['id']) ? 'Editar categoria' : 'Nova categoria';
$subtituloPagina = 'O identificador (slug) é gerado automaticamente a partir do nome';
require __DIR__ . '/../layout/header.php';
?>

<?php if ($erro): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

<div class="card" style="max-width:560px;">
  <form method="post" action="index.php?url=categoria/salvar">
    <?php if (!empty($categoria['id'])): ?>
      <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
    <?php endif; ?>

    <div class="form-row">
      <label for="nome">Nome da categoria</label>
      <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($categoria['nome'] ?? '') ?>" required autofocus>
    </div>

    <div class="form-row">
      <label for="descricao">Descrição (opcional)</label>
      <textarea id="descricao" name="descricao"><?= htmlspecialchars($categoria['descricao'] ?? '') ?></textarea>
    </div>

    <?php if (!empty($categoria['slug'])): ?>
      <p class="text-muted" style="font-size:12px;">Slug atual: <span class="mono"><?= htmlspecialchars($categoria['slug']) ?></span> (atualizado automaticamente se o nome mudar)</p>
    <?php endif; ?>

    <div class="row-actions" style="margin-top:8px;">
      <button type="submit" class="btn btn-primary">Salvar</button>
      <a href="index.php?url=categoria/index" class="btn btn-ghost">Cancelar</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
