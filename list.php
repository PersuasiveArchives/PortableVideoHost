<?php
include 'templates/header.php';

$dataFile = __DIR__ . '/data/videos.json';
$videos = json_decode(file_get_contents($dataFile), true) ?? [];

echo "<h2>All Videos</h2>";

if (empty($videos)) {
    echo "<p>No videos uploaded yet.</p>";
} else {
    echo "<ul style='list-style:none;padding-left:0;'>";

    foreach ($videos as $video) {
        $title = htmlspecialchars($video['title']);
        $id = urlencode($video['id']);

        echo "<li style='margin-bottom:15px;padding:10px;background:#f2f2f2;border-radius:5px;'>
                <a href='watch.php?id=$id' style='font-size:18px;color:#333;text-decoration:none;'>
                 Video: $title
                </a>
              </li>";
    }

    echo "</ul>";
}

include 'templates/footer.php';
