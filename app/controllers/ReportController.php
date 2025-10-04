<?php
namespace App\Controllers;
use App\Models\Product;
use App\Models\ReportLog;
use App\Helpers\PdfGenerator;

class ReportController {
    protected $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    public function index() {
        $rp = new ReportLog($this->pdo);
        $logs = $rp->all();
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/reports/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    public function generateInventoryPdf() {
        $productModel = new Product($this->pdo);
        $products = $productModel->all();
        ob_start();
        require __DIR__ . '/../views/reports/inventory_pdf.php';
        $html = ob_get_clean();
        $out = __DIR__ . '/../../storage/reports/inventory_' . time() . '.pdf';
        $pdf = new PdfGenerator();
        $pdf->htmlToPdf($html, $out);
        $log = new ReportLog($this->pdo);
        $log->create('inventory_pdf', json_encode([]), $out, $_SESSION['user']['id'] ?? 1);
        header('Location: /public/index.php?module=reports&action=index');
    }
}
