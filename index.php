<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/dash+xml');

// Get the MPD parameter from the URL
$get = $_GET['get'] ?? '';

if (!$get) {
    echo json_encode(["error" => "No MPD identifier provided"]);
    exit;
}

// Fetch the original MPD file from Astro
$mpdUrl = 'https://linearjitp-playback.astro.com.my/dash-wv/linear/' . $get;
$response = file_get_contents($mpdUrl);

if (!$response) {
    echo json_encode(["error" => "Failed to fetch MPD"]);
    exit;
}

// Add 1080p Representation
$representation1080p = '
<Representation bandwidth="3000000" height="1080" width="1920" codecs="avc1.640032" frameRate="25000/1000" id="164/video-cif-stream-6" />
';

// Insert 1080p inside the <AdaptationSet> for video
$mpd_modified = preg_replace(
    '/(<AdaptationSet[^>]*contentType="video"[^>]*>)/',
    '$1' . $representation1080p,
    $response
);

// Serve the modified MPD file
echo $mpd_modified;
?>
