<?php
function uploadMp3(array $file): array {
    // 1. Eroare PHP la upload
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return [false, 'Eroare la încărcarea fișierului.'];
    }
    // 2. Fișier încărcat real prin HTTP POST
    if (!is_uploaded_file($file['tmp_name'])) {
        return [false, 'Încărcare invalidă.'];
    }
    // 3. Dimensiune
    if ($file['size'] > MAX_SIZE) {
        return [false, 'Fișierul depășește 10 MB.'];
    }
    // 4. Extensie
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'mp3') {
        return [false, 'Sunt acceptate doar fișiere .mp3.'];
    }
    // 5. MIME real (din conținut, nu din ce trimite browserul)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ['audio/mpeg', 'audio/mp3'], true)) {
        return [false, 'Tipul fișierului nu este MP3.'];
    }
    // 6. Antet MP3 (ID3 sau frame sync 0xFFEx)
    $head = file_get_contents($file['tmp_name'], false, null, 0, 3);
    $isId3  = $head === 'ID3';
    $isSync = strlen($head) >= 2 && ord($head[0]) === 0xFF && (ord($head[1]) & 0xE0) === 0xE0;
    if (!$isId3 && !$isSync) {
        return [false, 'Antetul fișierului nu este de tip MP3.'];
    }
    // 7. Nume nou, generat de server
    $newName = bin2hex(random_bytes(8)) . '.mp3';
    if (!move_uploaded_file($file['tmp_name'], AUDIO_DIR . $newName)) {
        return [false, 'Nu s-a putut salva fișierul.'];
    }
    chmod(AUDIO_DIR . $newName, 0644);
    // 8. Salvăm numele original (curățat) pentru afișare
    $original = pathinfo($file['name'], PATHINFO_FILENAME);
    $original = mb_substr(preg_replace('/[^\p{L}\p{N} _\-().]/u', '', $original), 0, 100);
    $meta = readJson(META_FILE);
    $meta[$newName] = $original ?: 'Fără titlu';
    writeJson(META_FILE, $meta);

    return [true, 'Fișier încărcat cu succes.'];
}