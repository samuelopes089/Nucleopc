<?php
$tituloPagina = $fornecedor && !empty($fornecedor['id']) ? 'Editar fornecedor' : 'Novo fornecedor';
$subtituloPagina = 'Mantenha os dados de quem fornece mercadoria para a loja';
require __DIR__ . '/../layout/header.php';
?>

<?php if ($erro): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

<div class="card" style="max-width:640px;">
  <form method="post" action="index.php?url=fornecedor/salvar">
    <?php if (!empty($fornecedor['id'])): ?>
      <input type="hidden" name="id" value="<?= $fornecedor['id'] ?>">
    <?php endif; ?>

    <div class="form-row">
      <label for="nome">Nome / Razão Social</label>
      <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($fornecedor['nome'] ?? '') ?>" required>
    </div>

    <div class="form-grid">
      <div class="form-row">
        <label for="cnpj">CNPJ</label>
        <input type="text" id="cnpj" name="cnpj" value="<?= htmlspecialchars($fornecedor['cnpj'] ?? '') ?>" placeholder="00.000.000/0000-00">
      </div>
      <div class="form-row">
        <label for="telefone">Telefone</label>
        <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($fornecedor['telefone'] ?? '') ?>">
      </div>
    </div>

    <div class="form-row">
      <label for="email">E-mail</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($fornecedor['email'] ?? '') ?>">
    </div>

    <div class="form-grid">
      <div class="form-row">
        <label for="cep">CEP</label>
        <input type="text" id="cep" name="cep" value="<?= htmlspecialchars($fornecedor['cep'] ?? '') ?>" placeholder="00000-000">
      </div>
      <div class="form-row">
        <label for="endereco">Endereço</label>
        <input type="text" id="endereco" name="endereco" value="<?= htmlspecialchars($fornecedor['endereco'] ?? '') ?>">
      </div>
    </div>

    <div class="row-actions" style="margin-top:8px;">
      <button type="submit" class="btn btn-primary">Salvar</button>
      <a href="index.php?url=fornecedor/index" class="btn btn-ghost">Cancelar</a>
    </div>
  </form>
</div>

<script>
  buscarEnderecoPorCep('cep', 'endereco');
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
