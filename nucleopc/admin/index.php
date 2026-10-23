<?php
// Código desta parte do projeto Núcleo PC.
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pasta compartilhada de uploads (fica um nível acima de admin/ e loja/,
// como pasta irmã de ambos, para que as duas aplicações enxerguem a mesma imagem)
define('UPLOAD_DIR', __DIR__ . '/../uploads/produtos/');
define('UPLOAD_URL', '../uploads/produtos/');

require_once __DIR__ . '/core/App.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/config/mail.php';

new App();
