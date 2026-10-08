<?php
function getPlaylist(): array {
    $tracks = getTracks();
    $out = [];
    foreach (readJson(PLAYLIST_FILE) as $f) {
        if (isset($tracks[$f])) $out[] = $tracks[$f];
    }
    return $out;
}
function addToPlaylist(string $file): void {
    if (!isValidName($file) || !is_file(AUDIO_DIR . $file)) return;
    $pl = readJson(PLAYLIST_FILE);
    if (!in_array($file, $pl, true)) { $pl[] = $file; writeJson(PLAYLIST_FILE, $pl); }
}
function removeFromPlaylist(string $file): void {
    $pl = array_values(array_filter(readJson(PLAYLIST_FILE), fn($f) => $f !== $file));
    writeJson(PLAYLIST_FILE, $pl);
}
function movePlaylist(string $file, string $dir): void {
    $pl = readJson(PLAYLIST_FILE);
    $i = array_search($file, $pl, true);
    if ($i === false) return;
    $j = $dir === 'up' ? $i - 1 : $i + 1;
    if ($j < 0 || $j >= count($pl)) return;
    [$pl[$i], $pl[$j]] = [$pl[$j], $pl[$i]];
    writeJson(PLAYLIST_FILE, $pl);
}