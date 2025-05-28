<?php
include 'templates/header.php';

$dataFile = __DIR__ . '/data/videos.json';
$videos = json_decode(file_get_contents($dataFile), true) ?? [];

$id = $_GET['id'] ?? null;
$video = null;

foreach ($videos as $v) {
    if ($v['id'] === $id) {
        $video = $v;
        break;
    }
}

if (!$video) {
    echo "<p>Video not found.</p>";
    include 'templates/footer.php';
    exit;
}

$filename = $video['filename'];
$ext = pathinfo($filename, PATHINFO_EXTENSION);
$mime = ($ext === 'webm') ? 'video/webm' : 'video/mp4';

$videoDir = dirname(__DIR__ . "/videos/{$filename}");
$likesFile = "{$videoDir}/likes.txt";
$dislikesFile = "{$videoDir}/dislikes.txt";
$commentsFile = "{$videoDir}/comments.txt";

// This process verifies the prior existence of all necessary files, ensuring smooth operation and preventing errors.
if (!file_exists($likesFile)) file_put_contents($likesFile, '0');
if (!file_exists($dislikesFile)) file_put_contents($dislikesFile, '0');
if (!file_exists($commentsFile)) file_put_contents($commentsFile, '');

$likes = (int)file_get_contents($likesFile);
$dislikes = (int)file_get_contents($dislikesFile);

// LIKE AND DISLIKE!!!
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reaction'])) {
    if ($_POST['reaction'] === 'like') {
        file_put_contents($likesFile, $likes + 1);
    } elseif ($_POST['reaction'] === 'dislike') {
        file_put_contents($dislikesFile, $dislikes + 1);
    }
    header("Location: watch.php?id=" . urlencode($id) . "&sort=" . ($_GET['sort'] ?? 'newest'));
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_comment'])) {
    $commentText = trim($_POST['new_comment']);
    if ($commentText !== '') {
        $commentLine = date('Y-m-d H:i') . " | " . str_replace(["\r", "\n"], '', $commentText);
        file_put_contents($commentsFile, $commentLine . PHP_EOL, FILE_APPEND);
    }
    header("Location: watch.php?id=" . urlencode($id) . "&sort=" . ($_GET['sort'] ?? 'newest'));
    exit;
}

// This is a video player which essentially plays the video yk?
echo "<h2>" . htmlspecialchars($video['title']) . "</h2>";

echo "<div class='video-player' style='margin-bottom:20px;'>
        <video width='100%' controls>
            <source src='videos/{$filename}' type='{$mime}'>
            Your browser does not support the video tag.
        </video>
      </div>";


echo "<form method='post' style='margin:15px 0;'>
        <button type='submit' name='reaction' value='like'>👍 Like ($likes)</button>
        <button type='submit' name='reaction' value='dislike'>👎 Dislike ($dislikes)</button>
      </form>";

$sortOrder = $_GET['sort'] ?? 'newest';
echo "<div style='margin-bottom:10px;'>
        <strong>Sort by:</strong>
        <a href='watch.php?id={$id}&sort=newest'" . ($sortOrder === 'newest' ? " style='font-weight:bold;'" : "") . ">Newest</a> |
        <a href='watch.php?id={$id}&sort=oldest'" . ($sortOrder === 'oldest' ? " style='font-weight:bold;'" : "") . ">Oldest</a>
      </div>";


echo "<h3>Comments</h3>
<form method='post'>
    <textarea name='new_comment' required placeholder='Add a comment...' style='width:100%;height:80px;padding:10px;'></textarea><br>
    <button type='submit'>Post Comment</button>
</form>";


$commentLines = file($commentsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if ($sortOrder === 'newest') {
    $commentLines = array_reverse($commentLines);
}

if (!empty($commentLines)) {
    echo "<ul style='list-style:none;padding-left:0;'>";
    foreach ($commentLines as $line) {
        list($timestamp, $commentText) = explode(" | ", $line, 2);
        echo "<li style='margin-bottom:10px;padding:10px;background:#fff;border-radius:4px;'>
                <div style='font-size:12px;color:gray;margin-bottom:4px;'>  " . htmlspecialchars($timestamp) . "</div>
                <div style='font-size:16px;'>" . htmlspecialchars($commentText) . "</div>
              </li>";
    }
    echo "</ul>";
} else {
    echo "<p>No comments yet.</p>";
}

include 'templates/footer.php';
