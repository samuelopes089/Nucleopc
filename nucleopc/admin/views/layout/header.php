<?php
$rota = $_GET['url'] ?? 'dashboard';
$secao = explode('/', $rota)[0];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Núcleo PC · Painel</title>
  <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="shell">
  <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div class="brand-mark"></div>
      <div class="brand-text">
        <strong>Núcleo PC</strong>
        <span>painel interno</span>
      </div>
    </div>

    <nav class="nav-group">
      <div class="nav-label">OPERAÇÃO</div>
      <a href="index.php?url=dashboard" class="nav-link <?= $secao === 'dashboard' ? 'active' : '' ?>">
        <span class="nav-dot"></span> Dashboard
      </a>
      <a href="index.php?url=venda/index" class="nav-link <?= $secao === 'venda' ? 'active' : '' ?>">
        <span class="nav-dot"></span> Vendas (balcão)
      </a>
      <a href="index.php?url=entrada/index" class="nav-link <?= $secao === 'entrada' ? 'active' : '' ?>">
        <span class="nav-dot"></span> Entradas de estoque
      </a>

      <div class="nav-label">CADASTROS</div>
      <a href="index.php?url=produto/index" class="nav-link <?= $secao === 'produto' ? 'active' : '' ?>">
        <span class="nav-dot"></span> Produtos
      </a>
      <a href="index.php?url=categoria/index" class="nav-link <?= $secao === 'categoria' ? 'active' : '' ?>">
        <span class="nav-dot"></span> Categorias
      </a>
      <a href="index.php?url=fornecedor/index" class="nav-link <?= $secao === 'fornecedor' ? 'active' : '' ?>">
        <span class="nav-dot"></span> Fornecedores
      </a>

      <div class="nav-label">GESTÃO</div>
      <a href="index.php?url=relatorio/index" class="nav-link <?= $secao === 'relatorio' ? 'active' : '' ?>">
        <span class="nav-dot"></span> Relatórios
      </a>
      <?php if (($_SESSION['usuario_tipo'] ?? '') === 'admin'): ?>
        <a href="index.php?url=usuario/index" class="nav-link <?= $secao === 'usuario' ? 'active' : '' ?>">
          <span class="nav-dot"></span> Usuários
        </a>
      <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
      <a href="index.php?url=auth/logout" class="nav-link"><span class="nav-dot"></span> Sair</a>
    </div>
  </aside>

  <div>
    <header class="topbar">
      <div style="display:flex; align-items:center; gap:12px;">
        <button type="button" class="menu-toggle" id="menu-toggle" aria-label="Abrir menu" aria-controls="sidebar" aria-expanded="false">
          <span></span><span></span><span></span>
        </button>
        <div>
          <h1><?= $tituloPagina ?? 'Painel' ?></h1>
          <?php if (!empty($subtituloPagina)): ?><div class="sub"><?= $subtituloPagina ?></div><?php endif; ?>
        </div>
      </div>
      <div style="display:flex; align-items:center; gap:14px;">
        <a href="../loja/" target="_blank" rel="noopener" class="btn btn-ghost btn-sm" title="Abre a loja online em uma nova aba">
          ↗ Ver loja online
        </a>
        <div class="user-chip">
          <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?></strong>
          <span>· <?= htmlspecialchars($_SESSION['usuario_tipo'] ?? '') ?></span>
        </div>
      </div>
    </header>
    <main class="content">
