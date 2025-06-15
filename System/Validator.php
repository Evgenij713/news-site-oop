<?php
declare(strict_types=1);

namespace System;

class Validator{
    public array $scheme;

    public function __construct($scheme){
        $this->scheme = $scheme;
    }

    public function run(array $fields): array{
        $errors = [];
        foreach($fields as $name => $value){
            $rules = array_key_exists($name, $this->scheme) ? $this->scheme[$name] : null;
            if ($rules !== null) {
                if (strlen($value) < (int)$rules['min_length'] || strlen($value) > (int)$rules['max_length']) {
                    $errors[] = $rules['message'];
                }
            }
            if ($name === 'date_add') {
                if (!$this->validateDate($value)) {
                    $errors[] = 'Некорректный формат даты.';
                }
            }
            if ($name === 'email') {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Неверный формат email.';
                }
            }
            if ($name === 'login') {
                if (!$this->isValidLoginFormat($value)) {
                    $errors[] = 'Логин может содержать только следующие символы: буквы (a-z, A-Z), цифры (0-9), подчёркивание (_), дефис (-), точка (.).';
                }
            }
        }

        // Проверка совпадения паролей (если есть оба поля)
        if (isset($fields['password'], $fields['repeat-password'])) {
            if ($fields['password'] !== $fields['repeat-password']) {
                $errors[] = 'Введеные пароли не совпадают.';
            }
        }

        return $errors;
    }

    protected function validateDate(string $dateString): bool {
        $format = 'Y-m-d H:i:s';
        if (strtotime($dateString) === false) {
            return false;
        }
        else {
            $dateTime = date($format, strtotime($dateString));
            if ($dateString === $dateTime) {
                return true;
            }
            else {
                return false;
            }
        }
    }

    protected function isValidLoginFormat(string $login): bool {
        // Разрешены: буквы (a-z, A-Z), цифры (0-9), подчёркивание (_), дефис (-), точка (.)
        return preg_match('/^[a-zA-Z0-9_\-\.]+$/', $login) === 1;
    }
}