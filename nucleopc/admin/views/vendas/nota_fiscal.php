<?php
$tituloPagina = 'Nota Fiscal';
$subtituloPagina = 'Revise o documento antes de entregar ao cliente';
require __DIR__ . '/../layout/header.php';
?>

<div class="card" style="max-width:720px; background:#fff; color:#111; font-family: var(--sans);" id="nota-imprimivel">
  <div style="display:flex; justify-content:space-between; border-bottom:2px solid #111; padding-bottom:12px; margin-bottom:16px;">
    <div>
      <strong style="font-size:16px;">NÚCLEO PC LTDA</strong><br>
      <span style="font-size:12px;">CNPJ: 00.000.000/0001-00</span><br>
      <span style="font-size:12px;">Rua Principal, 123 - Centro - Nova Iguaçu/RJ</span>
    </div>
    <div style="text-align:right;">
      <strong>NOTA FISCAL</strong><br>
      <span style="font-size:12px;">Número: <?= $nota['numero'] ?></span><br>
      <span style="font-size:12px;">Série: <?= $nota['serie'] ?></span><br>
      <span style="font-size:12px;">Modelo: NFC-e</span>
    </div>
  </div>

  <div style="display:flex; gap:24px; font-size:12.5px; margin-bottom:14px;">
    <span><strong>Data de emissão:</strong> <?= date('d/m/Y H:i', strtotime($nota['data_emissao'])) ?></span>
    <span><strong>Referência venda:</strong> #<?= $pedido['id'] ?></span>
    <span><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente_nome'] ?? 'Cliente Balcão') ?></span>
  </div>

  <table style="font-size:12.5px;">
    <thead>
      <tr style="border-bottom:1px solid #ccc;">
        <th style="text-align:left; padding:6px 4px; color:#555;">Código</th>
        <th style="text-align:left; padding:6px 4px; color:#555;">Descrição do produto</th>
        <th style="text-align:right; padding:6px 4px; color:#555;">Qtd</th>
        <th style="text-align:right; padding:6px 4px; color:#555;">V. Unitário</th>
        <th style="text-align:right; padding:6px 4px; color:#555;">V. Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($itens as $item): ?>
        <tr style="border-bottom:1px solid #eee;">
          <td style="padding:6px 4px;"><?= htmlspecialchars($item['sku']) ?></td>
          <td style="padding:6px 4px;"><?= htmlspecialchars($item['produto_nome']) ?></td>
          <td style="padding:6px 4px; text-align:right;"><?= $item['quantidade'] ?></td>
          <td style="padding:6px 4px; text-align:right;">R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
          <td style="padding:6px 4px; text-align:right;">R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div style="display:flex; justify-content:flex-end; margin-top:16px;">
    <div style="background:#111; color:#fff; padding:10px 16px; text-align:right;">
      VALOR TOTAL DA NOTA<br>
      <strong style="font-size:18px;">R$ <?= number_format($nota['valor_total'], 2, ',', '.') ?></strong>
    </div>
  </div>
</div>

<div class="row-actions" style="margin-top:16px;">
  <button onclick="window.print()" class="btn btn-primary">Imprimir Nota Fiscal</button>
  <a href="index.php?url=venda/index" class="btn btn-ghost">Voltar</a>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
