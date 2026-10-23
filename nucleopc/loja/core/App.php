<?php
// Escolhe o controller e o método de acordo com a URL.
class App
{
    protected string $controller = 'HomeController';
    protected string $method = 'index';
    protected array $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        if (!empty($url[0])) {
            $controllerName = ucfirst(strtolower($url[0])) . 'Controller';
            $arquivoController = __DIR__ . '/../controllers/' . $controllerName . '.php';

            if (file_exists($arquivoController)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }
        require_once __DIR__ . '/../controllers/' . $this->controller . '.php';
        $controllerObj = new $this->controller();

        if (!empty($url[1]) && method_exists($controllerObj, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$controllerObj, $this->method], $this->params);
    }

    private function parseUrl(): array
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}
