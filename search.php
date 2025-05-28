<?php
include 'templates/header.php';

$dataFile = __DIR__ . '/data/videos.json';
$videos = json_decode(file_get_contents($dataFile), true) ?? [];

$searchQuery = trim($_GET['q'] ?? '');
$results = [];

if ($searchQuery !== '') {
    foreach ($videos as $video) {
        if (stripos($video['title'], $searchQuery) !== false) {
            $results[] = $video;
        }
    }
}

echo "<h2>Search Videos</h2>
<form method='get' action='search.php' style='margin-bottom:20px;'>
    <input type='text' name='q' value='" . htmlspecialchars($searchQuery) . "' placeholder='Search by title...' style='padding:8px;width:300px;' required>
    <button type='submit' style='padding:8px;'>Search</button>
</form>";

if ($searchQuery !== '') {
    echo "<h3>Results for: <em>" . htmlspecialchars($searchQuery) . "</em></h3>";

    if (!empty($results)) {
        echo "<ul style='list-style:none;padding-left:0;'>";

        foreach ($results as $video) {
            echo "<li style='margin-bottom:15px;background:#f9f9f9;padding:10px;border-radius:5px;'>
                    <a href='watch.php?id=" . urlencode($video['id']) . "' style='font-size:18px;text-decoration:none;color:#333;'>
                        " . htmlspecialchars($video['title']) . "
                    </a>
                  </li>";
        }

        echo "</ul>";
    } else {
        echo "<p>No videos found matching your query.</p>";
    }
}

include 'templates/footer.php';
