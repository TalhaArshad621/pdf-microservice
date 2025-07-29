<?php
namespace App;

use mikehaertl\wkhtmlto\Pdf;

class PdfService
{
    public static function generate(string $html, ?string $headerHtml = null): string
    {
        $pdfOptions = [
            'binary' => '/usr/local/bin/wkhtmltopdf',
            'disable-smart-shrinking',
            'print-media-type',
            'enable-local-file-access',
            'margin-top' => 60,
            'margin-bottom' => 10,
            'footer-right' => 'Developed by PlaneOps | Page [page] of [toPage]',
            'footer-font-size' => 9,
        ];

        // If headerHtml is provided, save it to a temp file
        if ($headerHtml) {
            $headerPath = tempnam(sys_get_temp_dir(), 'header_') . '.html';
            file_put_contents($headerPath, $headerHtml);
            $pdfOptions['header-html'] = $headerPath;
            $pdfOptions['header-spacing'] = 5;
        }

        $pdf = new Pdf($pdfOptions);
        $pdf->addPage($html);

        if (!$pdf->send(null)) {
            throw new \Exception($pdf->getError());
        }

        return $pdf->toString();
    }
}
