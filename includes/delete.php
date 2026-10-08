<?php
function deleteMp3(string $file): bool {
    $file = basename($file);
    if (!isValidName($file)) return false;
    $path = AUDIO_DIR . $file;
    if (!is_file($path)) return false;
    $ok = unlink($path);

    // curățăm metadatele și playlist-ul
    $meta = readJson(META_FILE); unset($meta[$file]); writeJson(META_FILE, $meta);
    $pl = readJson(PLAYLIST_FILE);
    writeJson(PLAYLIST_FILE, array_values(array_filter($pl, fn($f) => $f !== $file)));
    return $ok;
}