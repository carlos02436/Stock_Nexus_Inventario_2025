<?php
class GastoOperativo {
    private $db;
    private $table = 'gastos_operativos';

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Obtener todos los gastos operativos
     */
    public function obtenerTodos() {
        $query = "SELECT * FROM {$this->table} ORDER BY fecha DESC, id_gasto DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener gasto por ID
     */
    public function obtenerPorId($id_gasto) {
        $query = "SELECT * FROM {$this->table} WHERE id_gasto = :id_gasto";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_gasto', $id_gasto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nuevo gasto operativo
     */
    public function crear($datos) {
        $query = "INSERT INTO {$this->table} (fecha, categoria, descripcion, valor) 
                  VALUES (:fecha, :categoria, :descripcion, :valor)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha', $datos['fecha']);
        $stmt->bindParam(':categoria', $datos['categoria']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':valor', $datos['valor']);
        
        return $stmt->execute();
    }

    /**
     * Actualizar gasto operativo
     */
    public function actualizar($id_gasto, $datos) {
        $query = "UPDATE {$this->table} 
                  SET fecha = :fecha, categoria = :categoria, 
                      descripcion = :descripcion, valor = :valor 
                  WHERE id_gasto = :id_gasto";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fecha', $datos['fecha']);
        $stmt->bindParam(':categoria', $datos['categoria']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':valor', $datos['valor']);
        $stmt->bindParam(':id_gasto', $id_gasto, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Eliminar gasto operativo
     */
    public function eliminar($id_gasto) {
        $query = "DELETE FROM {$this->table} WHERE id_gasto = :id_gasto";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_gasto', $id_gasto, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtener total de gastos por mes
     */
    public function obtenerTotalPorMes($mes, $anio) {
        $query = "SELECT SUM(valor) as total FROM {$this->table} 
                  WHERE MONTH(fecha) = :mes AND YEAR(fecha) = :anio";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':mes', $mes, PDO::PARAM_INT);
        $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Obtener categorías únicas de gastos
     */
    public function obtenerCategorias() {
        $query = "SELECT DISTINCT categoria FROM {$this->table} WHERE categoria IS NOT NULL ORDER BY categoria";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>