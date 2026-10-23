<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recuperar senha · Núcleo PC</title>
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="brand">
      <div class="brand-mark"></div>
      <div class="brand-text">
        <strong>Núcleo PC</strong>
        <span>recuperar senha</span>
      </div>
    </div>

    <?php if ($mensagem): ?>
      <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
      <?php if ($linkTeste): ?>
        <p class="text-muted" style="font-size:11.5px; word-break:break-all;">
          Ambiente de teste local (sem SMTP configurado) — link de redefinição:<br>
          <a href="<?= htmlspecialchars($linkTeste) ?>"><?= htmlspecialchars($linkTeste) ?></a>
        </p>
      <?php endif; ?>
    <?php else: ?>
      <p class="text-muted" style="font-size:12.5px; margin-top:0;">Informe seu e-mail cadastrado. Enviaremos um link para você criar uma nova senha.</p>
      <form method="post" action="index.php?url=auth/esqueciSenha">
        <div class="form-row">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" required autofocus>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Enviar link de recuperação</button>
      </form>
    <?php endif; ?>

    <div style="text-align:center; margin-top:14px;">
      <a href="index.php?url=auth/login" style="font-size:12.5px;">Voltar para o login</a>
    </div>
  </div>
</div>
</body>
</html>
