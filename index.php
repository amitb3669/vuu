<?php
// Allow cross-origin access to support web-based players
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/dash+xml"); // Changed for DASH MPD
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Fetch the DASH stream URL
$get = isset($_GET['get']) ? $_GET['get'] : '';
if (!$get) {
    die("No stream identifier provided");
}

$mpdUrl = 'https://linearjitp-playback.astro.com.my/dash-wv/linear/' . $get;

$mpdheads = [
  'http' => [
      'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36\r\n",
      'follow_location' => 1,
      'timeout' => 5
  ]
];

$context = stream_context_create($mpdheads);
$res = file_get_contents($mpdUrl, false, $context);
if ($res === false) {
    die("Failed to fetch MPD file.");
}

// Output the DASH MPD response
echo $res;
?>
