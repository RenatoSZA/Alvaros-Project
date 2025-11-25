<?php
namespace Core;

class Router {
    protected array $routes = [];

    public function get(string $uri, $controller) {
        $this->add('GET', $uri, $controller);
    }

    public function post(string $uri, $controller) {
        $this->add('POST', $uri, $controller);
    }

    private function add(string $method, string $uri, $controller) {
        $uri = '/' . trim($uri, '/');
        $this->routes[$method][$uri] = $controller;
    }

    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        
        if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        $uri = '/' . trim($uri, '/');
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$method][$uri])) {
            $action = $this->routes[$method][$uri];

            if (is_callable($action)) {
                call_user_func($action);
                return;
            }

            if (is_string($action)) {
                [$controllerName, $methodName] = explode('@', $action);
                
                $controllerClass = "App\\Controllers\\" . $controllerName;

                if (!class_exists($controllerClass)) {
                    die("ERRO FATAL: O Controller <strong>$controllerClass</strong> não foi encontrado.<br>Verifique se o arquivo existe em <em>src/Controllers/$controllerName.php</em> e se o namespace está correto.");
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $methodName)) {
                    die("ERRO FATAL: O Método <strong>$methodName</strong> não existe dentro do controller <strong>$controllerName</strong>.");
                }
                $controller->$methodName();
                return;
            }
            die("Erro 500: Configuração inválida para a rota $uri");
        }
        
        //mexer nisso aqui dps
        http_response_code(404);
        echo "<h1>404 - Rota não encontrada</h1>";
        echo "<p>Você tentou acessar: <strong>$uri</strong></p>";
        echo "<p>Verifique se essa rota está definida no index.php</p>";
    }
}