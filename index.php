<?php
include 'templates/header.php';

$dataDir = __DIR__ . '/data';
$dataFile = $dataDir . '/videos.json';

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}

// The system will process the video entries at a time always.
$videos = json_decode(file_get_contents($dataFile), true) ?? [];

echo "<h2>Video Platform</h2>
<div style='margin-bottom: 20px; display: flex; align-items: center; gap: 10px;'>
    <form action='search.php' method='get' style='display: flex; gap: 10px; align-items: center;'>
        <input type='text' name='q' placeholder='Search videos by title...' style='padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px;' required>
        <button type='submit' style='padding: 8px 12px; background: red; color: white; border: none; border-radius: 4px; font-weight: bold;'>Search</button>
    </form>
    <a href='list.php' style='padding: 8px 12px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;'>All Videos</a>
</div>";
?>

<h3>Upload New Video</h3>
<form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Video Title" required><br><br>
    <input type="file" name="video" accept="video/mp4,video/webm" required><br><br>
    <button type="submit">Upload</button>
</form>

<?php include 'templates/footer.php'; ?>
