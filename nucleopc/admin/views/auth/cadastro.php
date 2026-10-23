<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Criar conta · Núcleo PC</title>
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="brand">
      <div class="brand-mark"></div>
      <div class="brand-text">
        <strong>Núcleo PC</strong>
        <span>criar conta</span>
      </div>
    </div>

    <?php if (!empty($erro)): ?>
      <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <p class="text-muted" style="font-size:12px; margin-top:0;">
      A conta criada aqui tem perfil <strong>vendedor</strong>. Para se tornar administrador, peça a um administrador já existente para alterar seu perfil em "Usuários".
    </p>

    <form method="post" action="index.php?url=auth/cadastro">
      <div class="form-row">
        <label for="nome">Nome completo</label>
        <input type="text" id="nome" name="nome" required autofocus>
      </div>
      <div class="form-row">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-row">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" minlength="6" required>
      </div>
      <div class="form-row">
        <label for="confirmacao">Confirmar senha</label>
        <input type="password" id="confirmacao" name="confirmacao" minlength="6" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Criar conta</button>
    </form>

    <div style="text-align:center; margin-top:14px;">
      <a href="index.php?url=auth/login" style="font-size:12.5px;">Já tenho conta — entrar</a>
    </div>
  </div>
</div>
</body>
</html>
