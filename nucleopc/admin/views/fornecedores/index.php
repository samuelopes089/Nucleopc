<?php
$tituloPagina = 'Fornecedores';
$subtituloPagina = 'Empresas que fornecem mercadoria para a loja';
require __DIR__ . '/../layout/header.php';
?>

<?php if ($mensagem): ?>
  <div class="alert alert-success">Fornecedor <?= htmlspecialchars($mensagem) ?> com sucesso.</div>
<?php endif; ?>

<div style="display:flex; justify-content:flex-end; margin-bottom:14px;">
  <a href="index.php?url=fornecedor/novo" class="btn btn-primary">+ Novo fornecedor</a>
</div>

<div class="card">
  <table>
    <thead>
      <tr><th>ID</th><th>Nome</th><th>CNPJ</th><th>Telefone</th><th>Endereço</th><th>Status</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($fornecedores as $f): ?>
        <tr>
          <td class="mono">#<?= $f['id'] ?></td>
          <td><?= htmlspecialchars($f['nome']) ?></td>
          <td class="mono"><?= htmlspecialchars($f['cnpj']) ?></td>
          <td><?= htmlspecialchars($f['telefone']) ?></td>
          <td class="text-muted"><?= htmlspecialchars($f['endereco']) ?></td>
          <td><span class="badge <?= $f['status'] === 'ativo' ? 'badge-ok' : 'badge-off' ?>"><?= $f['status'] ?></span></td>
          <td>
            <div class="row-actions">
              <a class="btn btn-ghost btn-sm" href="index.php?url=fornecedor/editar/<?= $f['id'] ?>">Editar</a>
              <?php if ($f['status'] === 'ativo'): ?>
                <a class="btn btn-danger btn-sm" href="index.php?url=fornecedor/inativar/<?= $f['id'] ?>">Inativar</a>
              <?php else: ?>
                <a class="btn btn-ghost btn-sm" href="index.php?url=fornecedor/ativar/<?= $f['id'] ?>">Ativar</a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
