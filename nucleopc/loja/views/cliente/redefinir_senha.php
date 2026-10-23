<?php
$tituloPagina = 'Redefinir senha — Núcleo PC';
require __DIR__ . '/../layout/header.php';
?>

<div class="container">
  <div class="form-card">
    <h1>Criar nova senha</h1>

    <?php if (!$valido): ?>
      <div class="alert alert-error">Este link de redefinição é inválido ou já expirou. Solicite um novo link.</div>
      <a href="index.php?url=cliente/esqueciSenha" class="btn btn-accent btn-block" style="margin-top:10px;">Solicitar novo link</a>
    <?php else: ?>
      <?php if ($erro): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
      <form method="post" action="index.php?url=cliente/redefinirSenha/<?= htmlspecialchars($token) ?>">
        <div class="form-row">
          <label for="senha">Nova senha</label>
          <input type="password" id="senha" name="senha" minlength="6" required autofocus>
        </div>
        <div class="form-row">
          <label for="confirmacao">Confirme a nova senha</label>
          <input type="password" id="confirmacao" name="confirmacao" minlength="6" required>
        </div>
        <button type="submit" class="btn btn-accent btn-block">Salvar nova senha</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
