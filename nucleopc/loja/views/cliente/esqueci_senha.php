<?php
$tituloPagina = 'Recuperar senha — Núcleo PC';
require __DIR__ . '/../layout/header.php';
?>

<div class="container">
  <div class="form-card">
    <h1>Recuperar senha</h1>
    <p class="sub">Informe seu e-mail cadastrado para receber o link de redefinição.</p>

    <?php if ($mensagem): ?>
      <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
      <?php if ($linkTeste): ?>
        <p style="font-size:11.5px; color:var(--muted); word-break:break-all;">
          Ambiente de teste local (sem SMTP configurado) — link de redefinição:<br>
          <a href="<?= htmlspecialchars($linkTeste) ?>"><?= htmlspecialchars($linkTeste) ?></a>
        </p>
      <?php endif; ?>
    <?php else: ?>
      <form method="post" action="index.php?url=cliente/esqueciSenha">
        <div class="form-row">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" required autofocus>
        </div>
        <button type="submit" class="btn btn-accent btn-block">Enviar link de recuperação</button>
      </form>
    <?php endif; ?>

    <p style="text-align:center; font-size:13px; color:var(--muted); margin-top:18px;">
      <a href="index.php?url=cliente/login">Voltar para o login</a>
    </p>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
