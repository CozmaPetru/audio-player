<?php
session_start();

define('AUDIO_DIR', __DIR__ . '/audio/');
define('AUDIO_URL', 'audio/');
define('META_FILE', __DIR__ . '/data/meta.json'); // numele originale
define('PLAYLIST_FILE', __DIR__ . '/data/playlist.json');
define('MAX_SIZE', 10 * 1024 * 1024); // 10 MB

function readJson(string $file): array
{
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}
function writeJson(string $file, array $data): void
{
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}
function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
function isValidName(string $f): bool
{
    return (bool) preg_match('/^[a-f0-9]{16}\.mp3$/', $f);
}
function flash(string $type, string $msg): void
{
    $_SESSION['flash'] = [$type, $msg];
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) require __DIR__ . '/vendor/autoload.php';
