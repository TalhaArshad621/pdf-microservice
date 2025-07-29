<?php
require __DIR__ . '/../vendor/autoload.php';

use App\PdfService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Only POST allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['html'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing html']);
    exit;
}

try {
    $pdfContent = PdfService::generate(
        $data['html'],
        $data['header_html'] ?? null
    );

    header('Content-Type: application/pdf');
    $filename = $data['filename'] ?? 'document.pdf';
    header("Content-Disposition: inline; filename=\"$filename\"");
    echo $pdfContent;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
