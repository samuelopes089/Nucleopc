<?php
$tituloPagina = 'Usuários do painel';
$subtituloPagina = 'Contas de acesso de administradores e vendedores';
require __DIR__ . '/../layout/header.php';
?>

<?php if ($mensagem === 'erro_auto_inativar'): ?>
  <div class="alert alert-error">Você não pode inativar o próprio usuário enquanto estiver logado com ele.</div>
<?php elseif ($mensagem): ?>
  <div class="alert alert-success">Usuário <?= htmlspecialchars($mensagem) ?> com sucesso.</div>
<?php endif; ?>

<div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
  <a href="index.php?url=usuario/novo" class="btn btn-primary">+ Novo usuário</a>
</div>

<div class="card">
  <table>
    <thead>
      <tr><th>ID</th><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($usuarios as $u): ?>
        <tr>
          <td class="mono">#<?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['nome']) ?></td>
          <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge <?= $u['tipo'] === 'admin' ? 'badge-ok' : 'badge-off' ?>"><?= $u['tipo'] ?></span></td>
          <td><span class="badge <?= $u['ativo'] ? 'badge-ok' : 'badge-off' ?>"><?= $u['ativo'] ? 'ativo' : 'inativo' ?></span></td>
          <td>
            <div class="row-actions">
              <a class="btn btn-ghost btn-sm" href="index.php?url=usuario/editar/<?= $u['id'] ?>">Editar</a>
              <?php if ($u['ativo']): ?>
                <a class="btn btn-danger btn-sm" href="index.php?url=usuario/inativar/<?= $u['id'] ?>">Inativar</a>
              <?php else: ?>
                <a class="btn btn-ghost btn-sm" href="index.php?url=usuario/ativar/<?= $u['id'] ?>">Ativar</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
