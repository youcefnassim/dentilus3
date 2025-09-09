<?php
// Simple .env loader: parse KEY=VALUE lines into $_ENV if .env present
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($k, $v) = explode('=', $line, 2);
        $k = trim($k); $v = trim($v);
        // remove optional surrounding quotes
        $v = preg_replace('/^(["\'])(.*)\\1$/', '$2', $v);
        $_ENV[$k] = $v;
        if (!isset($_SERVER[$k])) $_SERVER[$k] = $v;
    }
}

function env($key, $default = null) {
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];
    return $default;
}
