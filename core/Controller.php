<?php

namespace Core;

abstract class Controller
{
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function validate(array $rules): array
    {
        $errors = [];
        $data = [];

        foreach ($rules as $field => $ruleString) {
            $value = $this->input($field);
            $fieldRules = explode('|', $ruleString);

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $errors[$field] = __('validation.required', ['field' => $field]);
                    break;
                }
                if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = __('validation.email');
                    break;
                }
                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if ($value && strlen($value) < $min) {
                        $errors[$field] = __('validation.min', ['min' => $min]);
                        break;
                    }
                }
                if (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if ($value && strlen($value) > $max) {
                        $errors[$field] = __('validation.max', ['max' => $max]);
                        break;
                    }
                }
                if ($rule === 'numeric' && $value !== null && $value !== '' && !is_numeric($value)) {
                    $errors[$field] = __('validation.numeric');
                    break;
                }
            }

            $data[$field] = $value;
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $data);
            $this->back();
        }

        return $data;
    }

    protected function auditLog(string $action, ?string $resource = null, ?string $details = null): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO audit_log (user_id, action, resource, details, ip_address) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            Session::get('user_id'),
            $action,
            $resource,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        ]);
    }
}
