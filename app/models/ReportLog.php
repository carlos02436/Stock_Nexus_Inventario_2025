<?php
namespace App\Models;
use PDO;

class ReportLog extends Model {
    public function create($report_name, $params_json, $file_path, $created_by) {
        $stmt = $this->pdo->prepare('INSERT INTO reports_log (report_name,params_json,file_path,created_by) VALUES (?,?,?,?)');
        return $stmt->execute([$report_name,$params_json,$file_path,$created_by]);
    }
    public function all() {
        $stmt = $this->pdo->query('SELECT * FROM reports_log ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
