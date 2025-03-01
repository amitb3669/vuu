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

// Define the 1080p representations
$representation1080p_1 = '
<Representation bandwidth="3000000" height="1080" width="1920" codecs="avc1.640032" frameRate="25000/1000" id="164/video-cif-stream-6" />
';

$representation1080p_2 = '
<Representation bandwidth="4000000" height="1080" width="1920" codecs="avc1.64002a" frameRate="50000/1000" id="164/video-cif-stream-7" />
';

// Check if 1080p is already present
if (!str_contains($response, 'height="1080"')) {
    // Insert 1080p inside the <AdaptationSet> for video
    $mpd_modified = preg_replace(
        '/(<AdaptationSet[^>]*contentType="video"[^>]*>)/',
        '$1' . $representation1080p_1 . $representation1080p_2,
        $response
    );
} else {
    $mpd_modified = $response; // Keep the original if 1080p exists
}

// Serve the modified MPD file
echo $mpd_modified;
?>
