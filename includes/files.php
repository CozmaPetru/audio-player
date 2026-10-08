<?php
function formatDuration(float $sec): string {
    return sprintf('%d:%02d', intdiv((int)$sec, 60), (int)$sec % 60);
}
function formatSize(int $bytes): string {
    return $bytes >= 1048576 ? round($bytes / 1048576, 2) . ' MB' : round($bytes / 1024) . ' KB';
}

function getTracks(): array {
    $meta   = readJson(META_FILE);
    $tracks = [];
    $getID3 = class_exists('getID3') ? new getID3() : null;

    foreach (glob(AUDIO_DIR . '*.mp3') as $path) {
        $file = basename($path);
        if (!isValidName($file)) continue;

        $duration = '—'; $title = $meta[$file] ?? $file; $artist = ''; $bitrate = '';
        if ($getID3) {
            $info = $getID3->analyze($path);
            if (!empty($info['playtime_seconds'])) $duration = formatDuration($info['playtime_seconds']);
            if (!empty($info['audio']['bitrate'])) $bitrate = round($info['audio']['bitrate'] / 1000) . ' kbps';
            $tags = $info['tags']['id3v2'] ?? $info['tags']['id3v1'] ?? [];
            if (!empty($tags['title'][0]))  $title  = $tags['title'][0];
            if (!empty($tags['artist'][0])) $artist = $tags['artist'][0];
        }
        $tracks[$file] = [
            'file' => $file,
            'title' => $title,
            'artist' => $artist,
            'duration' => $duration,
            'bitrate' => $bitrate,
            'size' => formatSize(filesize($path)),
            'date' => date('d.m.Y H:i', filemtime($path)),
            'mtime' => filemtime($path),
        ];
    }
    uasort($tracks, fn($a, $b) => $b['mtime'] <=> $a['mtime']); // cele mai noi primele
    return $tracks;
}