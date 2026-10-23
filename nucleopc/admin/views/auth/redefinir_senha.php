<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Redefinir senha · Núcleo PC</title>
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="brand">
      <div class="brand-mark"></div>
      <div class="brand-text">
        <strong>Núcleo PC</strong>
        <span>nova senha</span>
      </div>
    </div>

    <?php if (!$valido): ?>
      <div class="alert alert-error">Este link de redefinição é inválido ou já expirou. Solicite um novo link.</div>
      <div style="text-align:center; margin-top:14px;">
        <a href="index.php?url=auth/esqueciSenha" class="btn btn-primary" style="width:100%; justify-content:center;">Solicitar novo link</a>
      </div>
    <?php else: ?>
      <?php if ($erro): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
      <form method="post" action="index.php?url=auth/redefinirSenha/<?= htmlspecialchars($token) ?>">
        <div class="form-row">
          <label for="senha">Nova senha</label>
          <input type="password" id="senha" name="senha" minlength="6" required autofocus>
        </div>
        <div class="form-row">
          <label for="confirmacao">Confirme a nova senha</label>
          <input type="password" id="confirmacao" name="confirmacao" minlength="6" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Salvar nova senha</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
