<?php
$tituloPagina = !empty($usuario['id']) ? 'Editar usuário' : 'Novo usuário';
$subtituloPagina = 'Cadastro de administradores e vendedores com acesso ao painel';
require __DIR__ . '/../layout/header.php';
?>

<?php if ($erro): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

<div class="card" style="max-width:560px;">
  <form method="post" action="index.php?url=usuario/salvar">
    <?php if (!empty($usuario['id'])): ?>
      <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
    <?php endif; ?>

    <div class="form-row">
      <label for="nome">Nome completo</label>
      <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome'] ?? '') ?>" required>
    </div>

    <div class="form-grid">
      <div class="form-row">
        <label for="email">E-mail de acesso</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" required>
      </div>
      <div class="form-row">
        <label for="tipo">Perfil</label>
        <select id="tipo" name="tipo">
          <option value="vendedor" <?= ($usuario['tipo'] ?? 'vendedor') === 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
          <option value="admin" <?= ($usuario['tipo'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-row">
        <label for="senha"><?= !empty($usuario['id']) ? 'Nova senha (deixe em branco para manter)' : 'Senha' ?></label>
        <input type="password" id="senha" name="senha" minlength="6" <?= empty($usuario['id']) ? 'required' : '' ?>>
      </div>
      <div class="form-row">
        <label for="confirmacao">Confirmar senha</label>
        <input type="password" id="confirmacao" name="confirmacao" minlength="6" <?= empty($usuario['id']) ? 'required' : '' ?>>
      </div>
    </div>

    <div class="row-actions" style="margin-top:8px;">
      <button type="submit" class="btn btn-primary">Salvar</button>
      <a href="index.php?url=usuario/index" class="btn btn-ghost">Cancelar</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
