<?php
require_once '../config/database.php';
require_once '../models/modelsDAO/AdminAssociadoDAO.php';

class adminsAssociadoHelper
{
    private $adminAssociadoDAO;

    public function __construct()
    {
        try {
            $db = (new Database())->getConnection();
            $this->adminAssociadoDAO = new AdminAssociadoDAO($db);
        } catch (Exception $e) {
            error_log("Error fatal en addminAssociadoHelper al conectar a BD: " . $e->getMessage());
        }
    }

    public function obtenerAssociadoPorId($id)
    {
        try {
            return $this->adminAssociadoDAO->obtenerPorId($id);
        } catch (Exception $e) {
            error_log("Error buscando emprendimiento $id: " . $e->getMessage());
            return null;
        }
    }
}
