<?php
declare(strict_types=1);

namespace Modules\Logs\Models;

class Index {
    protected static $instance;

    public static function getInstance() : static{
        if(static::$instance === null){
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected function __construct(){
    }

    // Функция проверки доступа
    public function checkPermissions(int $role): bool {
        return $role === 1;
    }

    public function saveLog(): bool {
        $res = false;

        $folder = 'logs';
        if (!file_exists($folder)) {
            mkdir($folder);
        }
        $today = date('Y-m-d');
        $fileLogs = fopen($folder . '/' . $today . '.logs', 'a');
        $log = date('Y-m-d H-i-s') . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . ($_SERVER['HTTP_REFERER'] ?? '-') . "\t" . $_SERVER['REQUEST_URI'] . "\n";
        if (fwrite($fileLogs, $log)) {
            $res = true;
        }
        fclose($fileLogs);

        return $res;
    }

    public function logStrToArr(string $str): array
    {
        $str = rtrim($str);
        $parts = explode("\t", $str);
        return ['dt' => $parts[0], 'ip' => $parts[1], 'referer' => $parts[2], 'uri' => $parts[3]];
    }

    public function getLogs(string $date): array
    {
        $logs = [];
        $file = 'logs/' . $date . '.logs';
        if (file_exists($file)) {
            $fileLogs = fopen($file, 'r');
            while (!feof($fileLogs)) {
                $log = fgets($fileLogs);
                if ($log !== false) {
                    $logs[] = $this->logStrToArr($log);
                }
            }
            fclose($fileLogs);
        }
        return $logs;
    }

    // Функция проверки url на sql-инъекции
    public function isValidUrl(string $url): bool
    {
        $symbols = ['%27', '%20', '"', '\'', ',', ';', '`', '~', '!', '@', '$', '№', '%', '^', '*',
            '(', ')', '|', '{', '}', '[', ']', '<', '>', '+', '*'];

        return array_reduce($symbols,
            fn(bool $carry, string $symbol) => $carry && !str_contains($url, $symbol),
            true
        );
    }
}