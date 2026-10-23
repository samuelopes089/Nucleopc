<?php
$tituloPagina = 'Entrar — Núcleo PC';
require __DIR__ . '/../layout/header.php';
?>

<div class="container">
  <div class="form-card">
    <h1>Entrar na minha conta</h1>
    <p class="sub">Acompanhe seus pedidos e finalize compras mais rápido.</p>

    <?php if (!empty($erro)): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <?php if (($_GET['msg'] ?? '') === 'senha_redefinida'): ?>
      <div class="alert alert-success">Senha redefinida com sucesso. Faça login com a nova senha.</div>
    <?php endif; ?>

    <form method="post" action="index.php?url=cliente/login">
      <div class="form-row">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required autofocus>
      </div>
      <div class="form-row">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required>
      </div>
      <button type="submit" class="btn btn-accent btn-block">Entrar</button>
    </form>

    <p style="text-align:center; font-size:12.5px; margin-top:12px;">
      <a href="index.php?url=cliente/esqueciSenha">Esqueci minha senha</a>
    </p>

    <p style="text-align:center; font-size:13px; color:var(--muted); margin-top:6px;">
      Ainda não tem conta? <a href="index.php?url=cliente/cadastro" style="color:var(--accent); font-weight:600;">Cadastre-se</a>
    </p>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
