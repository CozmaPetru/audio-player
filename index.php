<?php
   require 'config.php';
   require 'includes/files.php';
   require 'includes/playlist.php';
   require 'includes/icons.php';

$_SESSION['csrf'] ??= bin2hex(random_bytes(16));
$csrf     = $_SESSION['csrf'];
$tracks   = getTracks();
$playlist = getPlaylist();
$flash    = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="ro" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Player Audio MP3</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-paper: #faf7f2;
            --color-ink: #1c1917;
            --color-accent: #ea580c;
            --color-accent-dark: #c2410c;
        }
        /* Butoane reutilizabile */
        .btn-ghost {
            @apply inline-flex items-center justify-center gap-1.5 h-9 min-w-9 px-2.5 rounded-full text-sm font-medium
                   text-stone-600 bg-white border border-stone-200 hover:border-stone-400 hover:text-ink
                   transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent;
        }
        .btn-danger { @apply hover:bg-red-50 hover:border-red-300 hover:text-red-600; }
        .btn-play {
            @apply inline-flex items-center justify-center size-11 shrink-0 rounded-full bg-ink text-white
                   hover:bg-accent active:scale-95 transition
                   focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent;
        }
        .chip { @apply px-2 py-0.5 rounded-full bg-stone-100 text-stone-600 text-[11px] font-medium; }
    </style>
</head>

<body class="bg-paper text-ink min-h-screen pb-44 antialiased">

    <!-- Header -->
    <header class="border-b border-stone-200 bg-paper/90 backdrop-blur sticky top-0 z-20">
        <div class="max-w-6xl mx-auto px-4 h-16 flex items-center gap-3">
            <span class="grid place-items-center size-9 rounded-full bg-accent text-white">
                <?= icon('music', 'w-5 h-5') ?>
            </span>
            <h1 class="font-serif text-xl md:text-2xl font-semibold tracking-tight">Player Audio MP3</h1>
            <span class="ml-auto text-xs text-stone-500 hidden sm:block">
                <?= count($tracks) ?> piese · <?= count($playlist) ?> în playlist
            </span>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8 space-y-8">

        <?php if ($flash): ?>
            <div role="status" class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm
                <?= $flash[0] === 'success'
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                    : 'bg-red-50 border-red-200 text-red-800' ?>">
                <span class="size-2 rounded-full <?= $flash[0] === 'success' ? 'bg-emerald-500' : 'bg-red-500' ?>"></span>
                <?= h($flash[1]) ?>
            </div>
        <?php endif; ?>

        <!-- Upload -->
        <section class="rounded-2xl border-2 border-dashed border-stone-300 bg-white/60 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <h2 class="font-serif text-lg font-semibold">Încarcă un fișier MP3</h2>
                    <p class="text-sm text-stone-500">Dimensiune maximă 10 MB</p>
                </div>
                <form action="actions.php" method="post" enctype="multipart/form-data"
                      class="flex flex-col sm:flex-row gap-3 sm:w-3/5">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <input type="hidden" name="action" value="upload">
                    <input type="file" name="mp3" accept=".mp3,audio/mpeg" required
                           class="flex-1 min-w-0 text-sm text-stone-600 rounded-full border border-stone-300 bg-white overflow-hidden
                                  file:mr-3 file:py-2.5 file:px-4 file:border-0 file:bg-stone-100 file:text-stone-700
                                  file:cursor-pointer hover:file:bg-stone-200">
                    <button class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full font-medium
                                   bg-accent text-white hover:bg-accent-dark active:scale-95 transition">
                        <?= icon('upload', 'w-4 h-4') ?> Încarcă
                    </button>
                </form>
            </div>
        </section>

        <div class="grid lg:grid-cols-5 gap-6 items-start">

            <!-- Biblioteca -->
            <section class="lg:col-span-3 rounded-2xl bg-white border border-stone-200 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-stone-200">
                    <h2 class="font-serif text-lg font-semibold">Biblioteca</h2>
                    <span class="chip"><?= count($tracks) ?></span>
                </div>

                <?php if (!$tracks): ?>
                    <div class="px-5 py-12 text-center text-stone-500 text-sm">
                        <span class="inline-flex mb-3 text-stone-300"><?= icon('music', 'w-10 h-10') ?></span>
                        <p>Nu există fișiere încărcate.</p>
                    </div>
                <?php endif; ?>

                <ul class="divide-y divide-stone-100">
                    <?php foreach ($tracks as $t): ?>
                        <li class="px-4 sm:px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3 hover:bg-orange-50/40 transition">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <button class="play-btn btn-play" title="Redă" aria-label="Redă"
                                        data-src="<?= AUDIO_URL . h($t['file']) ?>" data-title="<?= h($t['title']) ?>" data-list="lib">
                                    <?= icon('play', 'w-4 h-4') ?>
                                </button>
                                <div class="min-w-0">
                                    <p class="font-semibold truncate"><?= h($t['title']) ?></p>
                                    <p class="text-sm text-stone-500 truncate"><?= h($t['artist'] ?: 'Artist necunoscut') ?></p>
                                    <p class="mt-1.5 flex flex-wrap gap-1.5">
                                        <span class="chip"><?= $t['duration'] ?></span>
                                        <span class="chip"><?= $t['size'] ?></span>
                                        <?php if ($t['bitrate']): ?><span class="chip"><?= $t['bitrate'] ?></span><?php endif; ?>
                                        <span class="chip"><?= $t['date'] ?></span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <form action="actions.php" method="post">
                                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                    <input type="hidden" name="file" value="<?= h($t['file']) ?>">
                                    <button name="action" value="pl_add" class="btn-ghost" title="Adaugă în playlist">
                                        <?= icon('plus', 'w-4 h-4') ?> <span class="hidden md:inline">Playlist</span>
                                    </button>
                                </form>
                                <a href="download.php?file=<?= h($t['file']) ?>" class="btn-ghost" title="Descarcă" aria-label="Descarcă">
                                    <?= icon('download', 'w-4 h-4') ?>
                                </a>
                                <form action="actions.php" method="post" onsubmit="return confirm('Ștergi fișierul?')">
                                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                    <input type="hidden" name="file" value="<?= h($t['file']) ?>">
                                    <button name="action" value="delete" class="btn-ghost btn-danger" title="Șterge" aria-label="Șterge">
                                        <?= icon('trash', 'w-4 h-4') ?>
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <!-- Playlist -->
            <section class="lg:col-span-2 lg:sticky lg:top-24 rounded-2xl bg-white border border-stone-200 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-stone-200 bg-orange-50/50">
                    <h2 class="font-serif text-lg font-semibold">Lista de redare</h2>
                    <span class="px-2 py-0.5 rounded-full bg-accent text-white text-[11px] font-semibold"><?= count($playlist) ?></span>
                </div>

                <?php if (!$playlist): ?>
                    <p class="px-5 py-10 text-center text-stone-500 text-sm">Playlist-ul este gol.</p>
                <?php endif; ?>

                <ol class="divide-y divide-stone-100">
                    <?php $i = 0; foreach ($playlist as $t): $i++; ?>
                        <li class="px-4 py-3 flex items-center gap-3 hover:bg-orange-50/40 transition">
                            <span class="w-5 text-right font-serif text-lg text-stone-300 tabular-nums"><?= $i ?></span>
                            <button class="play-btn btn-play !size-9" title="Redă" aria-label="Redă"
                                    data-src="<?= AUDIO_URL . h($t['file']) ?>" data-title="<?= h($t['title']) ?>" data-list="pl">
                                <?= icon('play', 'w-4 h-4') ?>
                            </button>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold truncate"><?= h($t['title']) ?></p>
                                <p class="text-xs text-stone-500"><?= $t['duration'] ?></p>
                            </div>
                            <form action="actions.php" method="post" class="flex gap-1">
                                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                                <input type="hidden" name="file" value="<?= h($t['file']) ?>">
                                <button name="action" value="pl_up" class="btn-ghost !h-8 !min-w-8 !px-1.5" title="Mută sus" aria-label="Mută sus"><?= icon('arrow-up', 'w-4 h-4') ?></button>
                                <button name="action" value="pl_down" class="btn-ghost !h-8 !min-w-8 !px-1.5" title="Mută jos" aria-label="Mută jos"><?= icon('arrow-down', 'w-4 h-4') ?></button>
                                <button name="action" value="pl_remove" class="btn-ghost btn-danger !h-8 !min-w-8 !px-1.5" title="Scoate din playlist" aria-label="Scoate din playlist"><?= icon('x', 'w-4 h-4') ?></button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </section>
        </div>
    </main>

    <!-- Player fix jos -->
    <div class="fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur border-t border-stone-200
                shadow-[0_-8px_30px_rgba(28,25,23,.08)] pb-[env(safe-area-inset-bottom)]">
        <div class="max-w-6xl mx-auto px-4 py-3 space-y-2">
            <p class="text-sm flex items-center gap-2 min-w-0 text-stone-600">
                <span class="text-accent"><?= icon('music', 'w-4 h-4') ?></span>
                <span id="now-playing" class="truncate font-medium">Selectează o melodie</span>
            </p>
            <audio id="player" controls class="w-full h-10 [color-scheme:light]"></audio>
        </div>
    </div>

    <script src="assets/js/player.js"></script>
</body>

</html>