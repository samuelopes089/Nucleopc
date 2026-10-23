<?php
// Funções que os controllers usam em comum.
class Controller
{
    protected function model(string $model)
    {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die('View não encontrada: ' . htmlspecialchars($view));
        }
    }

    /** Bloqueia o acesso caso o usuário não esteja logado */
    protected function requireLogin(): void
    {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: index.php?url=auth/login');
            exit;
        }
    }

    /** Bloqueia o acesso a áreas exclusivas do administrador */
    protected function requireAdmin(): void
    {
        $this->requireLogin();
        if (($_SESSION['usuario_tipo'] ?? '') !== 'admin') {
            http_response_code(403);
            die('Acesso restrito ao administrador.');
        }
    }

    /**
     * Redireciona para outra rota interna. Aceita uma querystring própria
     * (ex.: "auth/login?msg=ok") e a separa corretamente do parâmetro
     * "url" do roteador — caso contrário, "index.php?url=auth/login?msg=ok"
     * vira um único valor de URL ("auth/login?msg=ok"), o roteador não
     * encontra o método "login?msg=ok" e cai no método padrão "index()",
     * que não existe em todas as controllers (gerando erro fatal).
     */
    protected function redirect(string $url): void
    {
        // Se houver dados depois de ?, eles vão separados da rota.
        $partes = explode('?', $url, 2);
        $rota = $partes[0];
        $querystring = isset($partes[1]) ? $partes[1] : null;

        $destino = 'index.php?url=' . $rota;
        if ($querystring) {
            $destino .= '&' . $querystring;
        }
        header('Location: ' . $destino);
        exit;
    }
}
