<?php

namespace Core;

class Router
{
    private array $routes = [];
    private array $middleware = [];

    public function get(string $path, string $controller, string $method, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $controller, $method, $middleware);
    }

    public function post(string $path, string $controller, string $method, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $controller, $method, $middleware);
    }

    private function addRoute(string $httpMethod, string $path, string $controller, string $method, array $middleware): void
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'httpMethod' => $httpMethod,
            'pattern' => $pattern,
            'controller' => $controller,
            'method' => $method,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $requestMethod, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['httpMethod'] !== $requestMethod) {
                continue;
            }
            if (preg_match($route['pattern'], $uri, $matches)) {
                // Run middleware
                foreach ($route['middleware'] as $middlewareClass) {
                    $mw = new $middlewareClass();
                    if (!$mw->handle()) {
                        return;
                    }
                }

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $controllerClass = $route['controller'];
                $method = $route['method'];

                $controller = new $controllerClass();
                call_user_func_array([$controller, $method], $params);
                return;
            }
        }

        http_response_code(404);
        View::render('errors/404');
    }
}
