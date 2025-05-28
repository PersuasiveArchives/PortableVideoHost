<?php
include 'templates/header.php';

$dataFile = __DIR__ . '/data/videos.json';
$videos = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $video = $_FILES['video'];

    $allowedTypes = ['video/mp4', 'video/webm'];

    if (!in_array($video['type'], $allowedTypes)) {
        echo "<p style='color:red;'>Please select a valid MP4 or WebM file.</p>";
    } elseif ($video['error'] !== UPLOAD_ERR_OK) {
        echo "<p style='color:red;'>Upload error. Please try again.</p>";
    } else {
        $ext = pathinfo($video['name'], PATHINFO_EXTENSION);
        $safeTitle = preg_replace('/[^a-zA-Z0-9_\-]/', '_', strtolower($title));
        $uniqueName = uniqid('vid_', true) . '.' . $ext;
        $folderName = "{$safeTitle}_{$uniqueName}";
        $videoDir = __DIR__ . "/videos/{$folderName}";

        // Immediately create the folder if it doesn't exist
        if (!is_dir($videoDir)) {
            mkdir($videoDir, 0777, true);
        }

        
        $videoPath = "{$videoDir}/{$uniqueName}";
        move_uploaded_file($video['tmp_name'], $videoPath);

        
        file_put_contents("{$videoDir}/comments.txt", '');
        file_put_contents("{$videoDir}/likes.txt", '0');
        file_put_contents("{$videoDir}/dislikes.txt", '0');

        
        $videos[] = [
            'id' => uniqid(),
            'title' => $title,
            'filename' => "{$folderName}/{$uniqueName}"
        ];

        file_put_contents($dataFile, json_encode($videos, JSON_PRETTY_PRINT));
        echo "<p style='color:green;'>Upload successful! <a href='watch.php?id=" . end($videos)['id'] . "'>Watch it now</a>.</p>";
    }
}
?>

<h2>Upload a Video</h2>
<form action="" method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Video Title" required><br><br>
    <input type="file" name="video" accept="video/mp4,video/webm" required><br><br>
    <button type="submit">Upload</button>
</form>

<?php include 'templates/footer.php'; ?>
