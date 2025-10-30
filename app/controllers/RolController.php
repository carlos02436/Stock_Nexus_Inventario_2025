<?php
require_once 'app/models/Rol.php';

class RolController {
    private $rolModel;

    public function __construct($db) {
        $this->rolModel = new Rol($db);
    }

    public function listar() {
        return $this->rolModel->listar();
    }

    public function obtener($id) {
        return $this->rolModel->obtenerPorId($id);
    }

    public function crear($nombre_rol, $estado) {
        // Validar si ya existe el rol (solo por nombre, sin importar el estado)
        if ($this->rolModel->existeNombre($nombre_rol)) {
            return ['error' => 'El nombre del rol ya existe'];
        }

        // Crear nuevo rol
        $this->rolModel->crear($nombre_rol, $estado);
        return ['exito' => true];
    }

    public function editar($id_rol, $nombre_rol, $estado) {
        // Validar si ya existe otro rol con ese nombre
        if ($this->rolModel->existeNombre($nombre_rol, $id_rol)) {
            return ['error' => 'Ya existe otro rol con ese nombre'];
        }

        // Editar el rol
        $this->rolModel->editar($id_rol, $nombre_rol, $estado);
        return ['exito' => true];
    }

    public function cambiarEstado($id_rol, $accion) {
        $estado = ($accion === 'activar') ? 1 : 0;
        $this->rolModel->cambiarEstado($id_rol, $estado);
    }
}