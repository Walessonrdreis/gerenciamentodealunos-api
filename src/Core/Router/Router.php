<?php

namespace App\Core\Router;

class Router
{
    private array $routes = [];

    public function post(string $path, callable|array $handler): void
    {
        error_log("Registrando rota POST: " . $path);
        $this->routes['POST'][$path] = $handler;
    }

    public function get(string $path, callable|array $handler): void
    {
        error_log("Registrando rota GET: " . $path);
        $this->routes['GET'][$path] = $handler;
    }

    public function put(string $path, callable|array $handler): void
    {
        $this->routes['PUT'][$path] = $handler;
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->routes['DELETE'][$path] = $handler;
    }

    public function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Debug completo
        error_log("=== Debug do Router ===");
        error_log("Method: " . $method);
        error_log("Original Path: " . $path);
        
        // Remover o prefixo /api e garantir que o path comece com /
        $path = preg_replace('/^.*\/api/', '', $path);
        if (empty($path)) {
            $path = '/';
        } elseif ($path[0] !== '/') {
            $path = '/' . $path;
        }
        error_log("Path normalizado: " . $path);
        
        // Debug das rotas disponíveis
        error_log("Rotas registradas para " . $method . ":");
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            error_log("  - " . $route);
        }
        
        // Verificar rota exata
        if (isset($this->routes[$method][$path])) {
            error_log("Rota encontrada: " . $path);
            $handler = $this->routes[$method][$path];
            
            if (is_array($handler)) {
                [$controller, $method] = $handler;
                error_log("Executando controller: " . get_class($controller) . "@" . $method);
                $controller->$method();
            } else {
                $handler();
            }
            
            return;
        }

        // Verificar rotas com parâmetros
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
            $pattern = str_replace('/', '\/', $pattern);
            $pattern = '/^' . $pattern . '$/';
            
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove o match completo
                
                if (is_array($handler)) {
                    [$controller, $method] = $handler;
                    error_log("Executando controller com parâmetros: " . get_class($controller) . "@" . $method);
                    $controller->$method(...$matches);
                } else {
                    $handler(...$matches);
                }
                
                return;
            }
        }

        // Rota não encontrada
        error_log("Rota não encontrada: " . $path);
        http_response_code(404);
        echo json_encode([
            'error' => 'Rota não encontrada',
            'path' => $path,
            'method' => $method,
            'available_routes' => array_keys($this->routes[$method] ?? [])
        ]);
    }
} 