<?php
// CONFIG
$api_key   = '7658f4c4b8et4zqdlpko'; // <-- API key (jaga kerahasiaannya)
$file_path = __DIR__ . '/test.mp4';   // <-- ganti dengan path file yang mau diupload
$hx_api    = 'http://hxfile.co/api/upload/server';

// OPTIONAL: kalau file besar, naikin limits (jika environment mengizinkan)
@ini_set('upload_max_filesize','512M');
@ini_set('post_max_size','512M');
@ini_set('memory_limit','1024M');
@ini_set('max_execution_time','600');
@ini_set('max_input_time','600');

// --- STEP 1: Request server upload (GET)
$server_url = null;
$sess_id = null;

$ch = curl_init();
$qs = http_build_query(['key' => $api_key]);
curl_setopt_array($ch, [
    CURLOPT_URL => $hx_api . '?' . $qs,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);
$err = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
    die("Error fetching upload server: $err\n");
}

$resp_json = json_decode($response, true);
if (!$resp_json) {
    die("Invalid JSON response from hxfile server step 1. Raw response:\n$response\n");
}

if (isset($resp_json['status']) && $resp_json['status'] == 200) {
    // example: resp has 'sess_id' and 'result' which is server base URL
    $sess_id = isset($resp_json['sess_id']) ? $resp_json['sess_id'] : null;
    $server_url = isset($resp_json['result']) ? rtrim($resp_json['result'], '/') : null;
    if (!$sess_id || !$server_url) {
        die("Missing sess_id or server URL in response: " . json_encode($resp_json) . "\n");
    }
} else {
    die("Server refused request or returned error: " . json_encode($resp_json) . "\n");
}

// --- STEP 2: POST file to server_url (multipart/form-data)
if (!file_exists($file_path)) {
    die("File not found: $file_path\n");
}

// Prepare CURLFile (PHP 5.5+)
$cfile = new CURLFile($file_path);

// Some hxfile forms post to server root; use the server_url as action
$post_fields = [
    'sess_id' => $sess_id,
    'file'    => $cfile
];

$ch2 = curl_init();
curl_setopt_array($ch2, [
    CURLOPT_URL => $server_url,         // e.g. http://fs1.hxfile.co
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $post_fields,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 0,               // biarkan tanpa timeout untuk file besar, atur sesuai kebutuhan
    CURLOPT_VERBOSE => false,
]);

$response2 = curl_exec($ch2);
$err2 = curl_error($ch2);
$http_code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

if ($err2) {
    die("Error uploading file: $err2\n");
}

// hxfile may return HTML or JSON.
// Try to parse JSON, or show raw response.
$parsed = json_decode($response2, true);

if ($parsed && isset($parsed[0]['file_code'])) {
    $fileKey = $parsed[0]['file_code'];
    
    echo '<iframe src="https://xshotcok.com/embed-'.$fileKey.'.html" frameborder="0" scrolling="no" allowfullscreen="true" width="640" height="360"></iframe>';
    echo '<br>';
    echo '<a href="https://hxfile.co/'.$fileKey.'">Download</a>';
} else {
    // If response is not as expected, print it for debugging
    echo "Unexpected response from hxfile.co:<br>\n";
    if ($parsed) {
        echo "Could not find 'file_code'. Response:<br>\n";
        print_r($parsed);
    } else {
        echo $response2;
    }
}

?>
