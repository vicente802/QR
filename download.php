<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$filename = basename($_GET['fid'] ?? '');

if ($filename === '') {
    http_response_code(400);
    exit('Filename is required.');
}

$name = pathinfo($filename, PATHINFO_FILENAME);
$txtPath = __DIR__ . '/upload/' . $name . '.txt';

if (!is_file($txtPath)) {
    http_response_code(404);
    exit('TXT file not found.');
}

$text = file_get_contents($txtPath);

if ($text === false) {
    http_response_code(500);
    exit('Unable to read TXT file.');
}

$options = new Options();
$options->set('isRemoteEnabled', false);

$pdf = new Dompdf($options);

$html = '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11pt;
    }
    pre {
        white-space: pre-wrap;
        overflow-wrap: break-word;
    }
</style>
</head>
<body>
<pre>' .
htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') .
'</pre>
</body>
</html>';

$pdf->loadHtml($html, 'UTF-8');
$pdf->setPaper('A4', 'portrait');
$pdf->render();

// Automatically download the generated PDF
$pdf->stream($name . '.pdf', [
    'Attachment' => true
]);

exit;
?>