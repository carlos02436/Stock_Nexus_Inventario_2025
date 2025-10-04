<?php
namespace App\Helpers;
use Dompdf\Dompdf;

class PdfGenerator {
    public function htmlToPdf($html, $outputPath) {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        file_put_contents($outputPath, $dompdf->output());
        return $outputPath;
    }
}
