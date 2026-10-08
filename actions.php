<?php
require 'config.php';
require 'includes/upload.php';
require 'includes/files.php';
require 'includes/delete.php';
require 'includes/playlist.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

// Protecție CSRF
if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    flash('error', 'Cerere invalidă.'); header('Location: index.php'); exit;
}

$file = basename($_POST['file'] ?? '');

switch ($_POST['action'] ?? '') {
    case 'upload':
        [$ok, $msg] = isset($_FILES['mp3']) ? uploadMp3($_FILES['mp3']) : [false, 'Niciun fișier selectat.'];
        flash($ok ? 'success' : 'error', $msg);
        break;
    case 'delete':
        $ok = deleteMp3($file);
        flash($ok ? 'success' : 'error', $ok ? 'Fișier șters.' : 'Ștergerea a eșuat.');
        break;
    case 'pl_add': addToPlaylist($file); flash('success', 'Adăugat în playlist.'); break;
    case 'pl_remove': removeFromPlaylist($file); flash('success', 'Eliminat din playlist.'); break;
    case 'pl_up': movePlaylist($file, 'up'); break;
    case 'pl_down': movePlaylist($file, 'down'); break;
}
header('Location: index.php');