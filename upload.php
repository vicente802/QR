<?php

require_once __DIR__ . '/vendor/autoload.php';

use Smalot\PdfParser\Parser;

header('Content-Type: application/json');

$function = $_GET['function'] ?? '';
$topath   = __DIR__ . '/upload/';
$filename = basename($_GET['filename'] ?? 'upload.pdf');

// Ensure folder exists
if (!is_dir($topath)) {
    mkdir($topath, 0777, true);
}

$fullPath = $topath . $filename;

// Save uploaded binary data
$data = file_get_contents('php://input');

if ($data === false || file_put_contents($fullPath, $data) === false) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to save uploaded file.'
    ]);
    exit;
}

// Convert PDF to TXT
if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'pdf') {
    try {
        $parser = new Parser();
        $pdf = $parser->parseFile($fullPath);

        $text = $pdf->getText();

        $txtFilename = pathinfo($filename, PATHINFO_FILENAME) . '.txt';
        $txtPath = $topath . $txtFilename;

        if (file_put_contents($txtPath, $text) === false) {
            throw new RuntimeException('Failed to save TXT file.');
        }
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        echo json_encode([
            'status' => 'ok',
            'function' => $function,
            'file' => $txtFilename,
            'message' => 'PDF uploaded and converted to TXT successfully.'
        ]);

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'function' => $function,
            'message' => 'PDF conversion failed: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'ok',
        'function' => $function,
        'file' => $filename,
        'message' => 'File uploaded successfully.'
    ]);
}
?>
