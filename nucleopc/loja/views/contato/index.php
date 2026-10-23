<?php
$tituloPagina = 'Fale conosco — Núcleo PC';
require __DIR__ . '/../layout/header.php';
?>

<div class="container">
  <div class="form-card">
    <h1>Fale conosco</h1>
    <p class="sub">Dúvidas sobre um produto ou pedido? Envie sua mensagem.</p>

    <?php if (!empty($enviado)): ?>
      <div class="alert alert-success">Mensagem enviada com sucesso! Responderemos em breve.</div>
    <?php endif; ?>
    <?php if (!empty($erro)): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

    <form method="post" action="index.php?url=contato/enviar">
      <div class="form-row">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required>
      </div>
      <div class="form-row">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-row">
        <label for="assunto">Assunto</label>
        <input type="text" id="assunto" name="assunto">
      </div>
      <div class="form-row">
        <label for="mensagem">Mensagem</label>
        <textarea id="mensagem" name="mensagem" required></textarea>
      </div>
      <button type="submit" class="btn btn-accent btn-block">Enviar mensagem</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
