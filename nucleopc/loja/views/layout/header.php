<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../models/Carrinho.php';
$carrinhoAtual = new Carrinho();

if (!isset($categoriasMenu)) {
    require_once __DIR__ . '/../../models/Categoria.php';
    $categoriasMenu = (new Categoria())->listarTodas();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $tituloPagina ?? 'Núcleo PC — Loja de Computadores e Peças' ?></title>
  <link rel="stylesheet" href="assets/css/loja.css">
</head>
<body>

<div class="topbar-info">
  <div class="container">
    <span>Envio para todo o Brasil</span>
    <span>Compre com segurança · Núcleo PC</span>
  </div>
</div>

<header class="header">
  <div class="container">
    <a href="index.php?url=home/index" class="logo" aria-label="Núcleo PC">
      <img src="assets/img/logo-pc.png?v=2" alt="Logo Núcleo PC">
      <span class="logo-text">
        <strong>NÚCLEO PC</strong>
        <small>peças e computadores</small>
      </span>
    </a>

    <form class="search-form" action="index.php" method="get">
      <input type="hidden" name="url" value="produto/index">
      <input type="text" name="q" placeholder="Busque por processador, placa de vídeo, SSD..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
      <button type="submit">Buscar</button>
    </form>

    <div class="header-actions">
      <?php if (!empty($_SESSION['cliente_id'])): ?>
        <a href="index.php?url=cliente/meusPedidos">Olá, <?= htmlspecialchars($_SESSION['cliente_nome']) ?><br><small>meus pedidos</small></a>
        <a href="index.php?url=cliente/logout">Sair</a>
      <?php else: ?>
        <a href="index.php?url=cliente/login">Entrar / Cadastrar</a>
      <?php endif; ?>
      <a href="index.php?url=carrinho/index">
        Carrinho
        <span class="cart-badge"><?= $carrinhoAtual->quantidadeTotal() ?></span>
      </a>
    </div>
  </div>

  <nav class="category-bar">
    <div class="container">
      <a href="index.php?url=produto/index" class="<?= empty($_GET['categoria']) ? 'active' : '' ?>">Todos os produtos</a>
      <?php foreach ($categoriasMenu as $c): ?>
        <a href="index.php?url=produto/index&categoria=<?= $c['id'] ?>" class="<?= (isset($_GET['categoria']) && (int)$_GET['categoria'] === (int)$c['id']) ? 'active' : '' ?>">
          <?= htmlspecialchars($c['nome']) ?>
        </a>
      <?php endforeach; ?>
      <a href="index.php?url=contato/index">Contato</a>
    </div>
  </nav>
</header>
