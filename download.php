<?php
require 'config.php';
$file = basename($_GET['file'] ?? '');
if (!isValidName($file) || !is_file(AUDIO_DIR . $file)) { http_response_code(404); exit('Fișier inexistent.'); }

$meta = readJson(META_FILE);
$name = preg_replace('/[^\w\-. ]/u', '_', $meta[$file] ?? 'audio') . '.mp3';

header('Content-Type: audio/mpeg');
header('Content-Disposition: attachment; filename="' . $name . '"');
header('Content-Length: ' . filesize(AUDIO_DIR . $file));
readfile(AUDIO_DIR . $file);