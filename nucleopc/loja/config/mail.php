<?php
// Configurações usadas pelo sistema.
define('MAIL_REMETENTE_NOME', 'Núcleo PC');
define('MAIL_REMETENTE_EMAIL', 'nao-responda@nucleopc.com.br');

/**
 * Envia um e-mail simples em HTML usando a função nativa mail().
 * Retorna true se o PHP aceitou a mensagem para envio (não garante
 * a entrega — isso depende do servidor estar configurado).
 */
function enviarEmail(string $para, string $assunto, string $corpoHtml): bool
{
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . MAIL_REMETENTE_NOME . ' <' . MAIL_REMETENTE_EMAIL . ">\r\n";

    $enviado = @mail($para, $assunto, $corpoHtml, $headers);

    // Fallback para ambiente de desenvolvimento (XAMPP sem SMTP configurado):
    // grava o e-mail em um arquivo de log para consulta manual/testes.
    $logDir = __DIR__ . '/../storage';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
    }
    $linha = sprintf(
        "[%s] Para: %s | Assunto: %s\n%s\n%s\n",
        date('d/m/Y H:i:s'),
        $para,
        $assunto,
        $corpoHtml,
        str_repeat('-', 60)
    );
    @file_put_contents($logDir . '/emails.log', $linha, FILE_APPEND);

    return $enviado;
}
