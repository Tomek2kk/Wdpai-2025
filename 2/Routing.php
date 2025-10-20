<?php

class Routing {

    private array $routes = [];

    public function get(string $path, callable $callback): void {
        $this->addRoute('GET', $path, $callback);
    }

    public function post(string $path, callable $callback): void {
        $this->addRoute('POST', $path, $callback);
    }

    public function put(string $path, callable $callback): void {
        $this->addRoute('PUT', $path, $callback);
    }

    public function patch(string $path, callable $callback): void {
        $this->addRoute('PATCH', $path, $callback);
    }

    public function delete(string $path, callable $callback): void {
        $this->addRoute('DELETE', $path, $callback);
    }

    public function options(string $path, callable $callback): void {
        $this->addRoute('OPTIONS', $path, $callback);
    }

    public function head(string $path, callable $callback): void {
        $this->addRoute('HEAD', $path, $callback);
    }

    public function addRoute(string $method, string $path, callable $callback): void {
        $this->routes[] = [
            'method' => $method,
            'path'   => $this->normalizePath($path),
            'callback' => $callback,
        ];
    }

    public function dispatch() {
        $requestUri = $this->normalizePath(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            $pattern = preg_replace('#\{([\w]+)\}#', '(?P<\1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func_array($route['callback'], $params);
                return;
            }
        }

        include 'public/views/404.html';
    }

    private function normalizePath(string $path): string {
        return '/' . trim($path, '/');
    }

    /*public static function run(string $path) {
        switch($path) {
            case 'login':
                include 'public/views/login.html';
                break;
            case 'dashboard':
                include 'public/views/dashboard.html';
                break;
            default:
                include 'public/views/404.html';
                break;
        }
    }*/
}