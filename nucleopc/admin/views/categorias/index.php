<?php
$tituloPagina = 'Categorias';
$subtituloPagina = 'Categorias usadas no cadastro de produtos e nos filtros da loja online';
require __DIR__ . '/../layout/header.php';
?>

<?php if ($mensagem): ?>
  <div class="alert alert-success">Categoria <?= htmlspecialchars($mensagem) ?> com sucesso.</div>
<?php endif; ?>
<?php if ($erro): ?>
  <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
<?php endif; ?>

<div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
  <a href="index.php?url=categoria/novo" class="btn btn-primary">+ Nova categoria</a>
</div>

<div class="card">
  <table>
    <thead>
      <tr><th>ID</th><th>Nome</th><th>Slug</th><th>Descrição</th><th class="text-right">Produtos</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (empty($categorias)): ?>
        <tr><td colspan="6" class="text-muted">Nenhuma categoria cadastrada.</td></tr>
      <?php else: foreach ($categorias as $c): ?>
        <tr>
          <td class="mono">#<?= $c['id'] ?></td>
          <td><?= htmlspecialchars($c['nome']) ?></td>
          <td class="mono text-muted"><?= htmlspecialchars($c['slug']) ?></td>
          <td class="text-muted"><?= htmlspecialchars($c['descricao'] ?: '—') ?></td>
          <td class="text-right"><span class="badge badge-ok"><?= $c['total_produtos'] ?></span></td>
          <td>
            <div class="row-actions">
              <a class="btn btn-ghost btn-sm" href="index.php?url=categoria/editar/<?= $c['id'] ?>">Editar</a>
              <?php if ((int) $c['total_produtos'] === 0): ?>
                <a class="btn btn-danger btn-sm" href="index.php?url=categoria/excluir/<?= $c['id'] ?>"
                   onclick="return confirm('Excluir esta categoria?');">Excluir</a>
              <?php else: ?>
                <span class="text-muted" style="font-size:11.5px;" title="Só é possível excluir categorias sem produtos">Em uso</span>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
