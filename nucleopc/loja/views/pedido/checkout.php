<?php
$tituloPagina = 'Finalizar compra — Núcleo PC';
require __DIR__ . '/../layout/header.php';

$enderecoSugerido = trim(($cliente['endereco'] ?? '') . ', ' . ($cliente['numero'] ?? '') . ' - ' . ($cliente['bairro'] ?? '') . ' - ' . ($cliente['cidade'] ?? '') . '/' . ($cliente['uf'] ?? ''), ', -');
?>

<div class="container">
  <section class="section">
    <div class="section-head"><h2>Finalizar compra</h2></div>

    <?php if (!empty($erro)): ?><div class="alert alert-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

    <div class="cart-layout">
      <form method="post" action="index.php?url=pedido/finalizar">
        <div class="form-card wide" style="margin:0;">
          <h1 style="font-size:17px;">Endereço de entrega</h1>
          <div class="form-row">
            <label for="endereco_entrega">Endereço completo</label>
            <textarea id="endereco_entrega" name="endereco_entrega" required><?= htmlspecialchars($enderecoSugerido) ?></textarea>
          </div>

          <h1 style="font-size:17px; margin-top:22px;">Forma de pagamento</h1>
          <div class="mp-info">
            <span class="mp-badge">Mercado Pago</span>
            <p>Ao confirmar, você será redirecionado ao <strong>Mercado Pago</strong> para escolher e concluir o pagamento com segurança — Pix, cartão de crédito/débito ou boleto. O pedido só é confirmado depois que o pagamento é aprovado.</p>
          </div>

          <button type="submit" class="btn btn-accent btn-block" style="margin-top:16px;">Ir para o pagamento</button>
        </div>
      </form>

      <aside class="cart-summary">
        <h3 style="margin-top:0; font-size:14px;">Resumo</h3>
        <?php foreach ($itens as $item): ?>
          <div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:8px;">
            <span><?= $item['quantidade'] ?>x <?= htmlspecialchars($item['nome']) ?></span>
            <span>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></span>
          </div>
        <?php endforeach; ?>
        <div class="total-row">
          <span>Total</span>
          <span>R$ <?= number_format($total, 2, ',', '.') ?></span>
        </div>
      </aside>
    </div>
  </section>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
