<?php
// Allow cross-origin access to support web-based players
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/vnd.apple.mpegurl");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Fetch the HLS stream URL
$stream_url = isset($_GET['url']) ? $_GET['url'] : '';
$get = $_GET['get'];
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
echo $res;

if (!$stream_url) {
    die("No stream URL provided");
}

// Validate the HLS URL
if (!filter_var($stream_url, FILTER_VALIDATE_URL) || !str_contains($stream_url, ".m3u8")) {
    die("Invalid HLS stream URL");
}

// Fetch and relay the HLS stream
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $stream_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.82 Safari/537.36"
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    die("Failed to fetch the HLS stream.");
}

echo $response;
?>
