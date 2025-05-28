<?php
$commentsFile = __DIR__ . '/data/comments.json';

$id = $_POST['id'] ?? '';
$index = $_POST['index'] ?? '';
$action = $_POST['action'] ?? '';

if (!file_exists($commentsFile)) {
    die("No comment file found.");
}

$data = json_decode(file_get_contents($commentsFile), true);

if (isset($data[$id][$index])) {
    if ($action === 'like') {
        $data[$id][$index]['likes'] += 1;
    } elseif ($action === 'dislike') {
        $data[$id][$index]['dislikes'] += 1;
    }
    file_put_contents($commentsFile, json_encode($data, JSON_PRETTY_PRINT));
}

header("Location: watch.php?id=" . urlencode($id));
exit;
