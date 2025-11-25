<?php
namespace Core;

class Router {
    protected array $routes = [];

    // Removi o "string" do tipo do $controller para aceitar funções também
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
        // 1. Pega a URL digitada no navegador
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // 2. DETECTA SUBPASTAS (O Pulo do Gato)
        // Descobre onde o index.php está fisicamente (ex: /projects/LumiG/public)
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        
        // Se a URL começa com esse caminho de pasta, remove ele
        // Ex: Transforma "/projects/LumiG/public/login" em "/login"
        if ($scriptDir !== '/' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        // Garante que sempre começa com / e não termina com /
        $uri = '/' . trim($uri, '/');
        $method = $_SERVER['REQUEST_METHOD'];

        // --- MODO DEBUGAÇÃO (Se der erro, descomente as linhas abaixo pra ver o que tá rolando) ---
        // echo "Rota Acessada: " . $uri . "<br>";
        // echo "Método: " . $method . "<br>";
        // echo "<pre>"; print_r($this->routes); echo "</pre>";
        // die(); 
        // -----------------------------------------------------------------------------------------

        if (isset($this->routes[$method][$uri])) {
            $action = $this->routes[$method][$uri];

            if (is_callable($action)) {
                call_user_func($action);
                return;
            }

            if (is_string($action)) {
                [$controllerName, $methodName] = explode('@', $action);
                
                // Monta o nome completo: App\Controllers\AuthController
                $controllerClass = "App\\Controllers\\" . $controllerName;

                // VERIFICAÇÃO DETALHADA
                if (!class_exists($controllerClass)) {
                    die("ERRO FATAL: O Controller <strong>$controllerClass</strong> não foi encontrado.<br>Verifique se o arquivo existe em <em>src/Controllers/$controllerName.php</em> e se o namespace está correto.");
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $methodName)) {
                    die("ERRO FATAL: O Método <strong>$methodName</strong> não existe dentro do controller <strong>$controllerName</strong>.");
                }

                // Se passou por tudo, executa
                $controller->$methodName();
                return;
            }
            die("Erro 500: Configuração inválida para a rota $uri");
        }
        
        // Personalize sua página 404 aqui
        http_response_code(404);
        echo "<h1>404 - Rota não encontrada</h1>";
        echo "<p>Você tentou acessar: <strong>$uri</strong></p>";
        echo "<p>Verifique se essa rota está definida no index.php</p>";
    }
}