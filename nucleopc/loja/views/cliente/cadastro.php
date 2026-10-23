<?php
$tituloPagina = 'Criar conta — Núcleo PC';
require __DIR__ . '/../layout/header.php';
?>

<div class="container">
  <div class="form-card wide">
    <h1>Criar minha conta</h1>
    <p class="sub">Preencha seus dados para comprar na Núcleo PC.</p>

    <?php if (!empty($erro)): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

    <form method="post" action="index.php?url=cliente/cadastro">
      <div class="form-grid-2">
        <div class="form-row">
          <label for="nome">Nome completo</label>
          <input type="text" id="nome" name="nome" required>
        </div>
        <div class="form-row">
          <label for="cpf">CPF</label>
          <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00">
        </div>
      </div>

      <div class="form-grid-2">
        <div class="form-row">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="form-row">
          <label for="telefone">Telefone</label>
          <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000">
        </div>
      </div>

      <div class="form-row">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required>
      </div>

      <div class="form-grid-3">
        <div class="form-row">
          <label for="cep">CEP</label>
          <input type="text" id="cep" name="cep" placeholder="00000-000">
        </div>
        <div class="form-row">
          <label for="numero">Número</label>
          <input type="text" id="numero" name="numero">
        </div>
        <div class="form-row">
          <label for="complemento">Complemento</label>
          <input type="text" id="complemento" name="complemento">
        </div>
      </div>

      <div class="form-row">
        <label for="endereco">Endereço</label>
        <input type="text" id="endereco" name="endereco">
      </div>

      <div class="form-grid-3">
        <div class="form-row">
          <label for="bairro">Bairro</label>
          <input type="text" id="bairro" name="bairro">
        </div>
        <div class="form-row">
          <label for="cidade">Cidade</label>
          <input type="text" id="cidade" name="cidade">
        </div>
        <div class="form-row">
          <label for="uf">UF</label>
          <input type="text" id="uf" name="uf" maxlength="2">
        </div>
      </div>

      <button type="submit" class="btn btn-accent btn-block">Criar conta</button>
    </form>

    <p style="text-align:center; font-size:13px; color:var(--muted); margin-top:18px;">
      Já tem conta? <a href="index.php?url=cliente/login" style="color:var(--accent); font-weight:600;">Entrar</a>
    </p>
  </div>
</div>

<script>
  document.getElementById('cep').addEventListener('blur', function () {
    const cep = this.value.replace(/\D/g, '');
    if (cep.length !== 8) return;
    fetch('https://viacep.com.br/ws/' + cep + '/json/')
      .then((r) => r.json())
      .then((data) => {
        if (!data.erro) {
          document.getElementById('endereco').value = data.logradouro;
          document.getElementById('bairro').value = data.bairro;
          document.getElementById('cidade').value = data.localidade;
          document.getElementById('uf').value = data.uf;
        }
      });
  });
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
