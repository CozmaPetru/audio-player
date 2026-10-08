<?php
require 'config.php';
require 'includes/files.php';
require 'includes/playlist.php';

$_SESSION['csrf'] ??= bin2hex(random_bytes(16));
$csrf     = $_SESSION['csrf'];
$tracks   = getTracks();
$playlist = getPlaylist();
$flash    = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="ro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Player Audio MP3</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-gray-100 text-gray-800 min-h-screen pb-40">

    <header class="bg-indigo-600 text-white p-4 shadow">
        <h1 class="text-xl md:text-2xl font-bold max-w-6xl mx-auto">🎵 Player Audio MP3</h1>
    </header>

    <main class="max-w-6xl mx-auto p-4 space-y-6">

        <?php if ($flash): ?>
            <div class="p-3 rounded <?= $flash[0] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= h($flash[1]) ?>
            </div>
        <?php endif; ?>

        <!-- Upload -->
        <section class="bg-white rounded-lg shadow p-4">
            <h2 class="font-semibold mb-3">Încarcă un fișier MP3 (max. 10 MB)</h2>
            <form action="actions.php" method="post" enctype="multipart/form-data"
                class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="upload">
                <input type="file" name="mp3" accept=".mp3,audio/mpeg" required
                    class="flex-1 border rounded p-2 text-sm">
                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Încarcă</button>
            </form>
        </section>

        <div class="grid lg:grid-cols-2 gap-6">

            <!-- Biblioteca -->
            <section class="bg-white rounded-lg shadow p-4">
                <h2 class="font-semibold mb-3">Biblioteca (<?= count($tracks) ?>)</h2>
                <?php if (!$tracks): ?><p class="text-gray-500 text-sm">Nu există fișiere încărcate.</p><?php endif; ?>
                <ul class="divide-y">
                    <?php foreach ($tracks as $t): ?>
                        <li class="py-3 flex flex-col sm:flex-row sm:items-center gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium truncate"><?= h($t['title']) ?></p>
                                <p class="text-xs text-gray-500">
                                    <?= h($t['artist'] ?: 'Artist necunoscut') ?> · <?= $t['duration'] ?> · <?= $t['size'] ?>
                                    <?= $t['bitrate'] ? '· ' . $t['bitrate'] : '' ?> · <?= $t['date'] ?>
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-1 text-sm">
                                <button class="play-btn px-2 py-1 bg-green-500 text-white rounded"
                                    data-src="<?= AUDIO_URL . h($t['file']) ?>" data-title="<?= h($t['title']) ?>" data-list="lib">▶</button>
                                <a href="download.php?file=<?= h($t['file']) ?>" class="px-2 py-1 bg-blue-500 text-white rounded">⬇</a>
                                <form action="actions.php" method="post">
                                    <input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="file" value="<?= h($t['file']) ?>">
                                    <button name="action" value="pl_add" class="px-2 py-1 bg-indigo-500 text-white rounded">＋ Playlist</button>
                                </form>
                                <form action="actions.php" method="post" onsubmit="return confirm('Ștergi fișierul?')">
                                    <input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="file" value="<?= h($t['file']) ?>">
                                    <button name="action" value="delete" class="px-2 py-1 bg-red-500 text-white rounded">🗑</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <!-- Playlist -->
            <section class="bg-white rounded-lg shadow p-4">
                <h2 class="font-semibold mb-3">Lista de redare (<?= count($playlist) ?>)</h2>
                <?php if (!$playlist): ?><p class="text-gray-500 text-sm">Playlist-ul este gol.</p><?php endif; ?>
                <ol class="divide-y list-decimal list-inside">
                    <?php foreach ($playlist as $t): ?>
                        <li class="py-3">
                            <span class="font-medium"><?= h($t['title']) ?></span>
                            <span class="text-xs text-gray-500">(<?= $t['duration'] ?>)</span>
                            <div class="flex flex-wrap gap-1 mt-1 text-sm">
                                <button class="play-btn px-2 py-1 bg-green-500 text-white rounded"
                                    data-src="<?= AUDIO_URL . h($t['file']) ?>" data-title="<?= h($t['title']) ?>" data-list="pl">▶</button>
                                <form action="actions.php" method="post" class="flex gap-1">
                                    <input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="file" value="<?= h($t['file']) ?>">
                                    <button name="action" value="pl_up" class="px-2 py-1 bg-gray-300 rounded">↑</button>
                                    <button name="action" value="pl_down" class="px-2 py-1 bg-gray-300 rounded">↓</button>
                                    <button name="action" value="pl_remove" class="px-2 py-1 bg-red-400 text-white rounded">✕</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </section>
        </div>
    </main>

    <!-- Player fix jos -->
    <div class="fixed bottom-0 inset-x-0 bg-gray-900 text-white p-3 shadow-lg">
        <div class="max-w-6xl mx-auto">
            <p id="now-playing" class="text-sm mb-2 truncate">Selectează o melodie</p>
            <audio id="player" controls class="w-full"></audio>
        </div>
    </div>

    <script src="assets/js/player.js"></script>
</body>

</html>