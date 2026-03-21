<?php

namespace Core;

class View
{
    private static string $layout = 'layouts/main';

    public static function setLayout(string $layout): void
    {
        self::$layout = $layout;
    }

    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        $data['csrf_token'] = Session::generateCsrfToken();
        $data['errors'] = Session::getFlash('errors', []);
        $data['old'] = Session::getFlash('old', []);
        $data['success'] = Session::getFlash('success');
        $data['user'] = self::getAuthUser();

        extract($data);

        $viewPath = self::resolvePath($view);
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $layoutPath = self::resolvePath($layout ?? self::$layout);
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    public static function partial(string $view, array $data = []): void
    {
        extract($data);
        $path = self::resolvePath($view);
        if (file_exists($path)) {
            require $path;
        }
    }

    private static function resolvePath(string $view): string
    {
        $base = dirname(__DIR__) . '/app/Views/';
        return $base . str_replace('.', '/', $view) . '.php';
    }

    private static function getAuthUser(): ?array
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return null;
        }
        return Session::get('user_data');
    }
}
