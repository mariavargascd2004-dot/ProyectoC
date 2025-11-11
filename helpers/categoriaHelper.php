<?php
require_once '../config/database.php';
require_once '../models/modelsDAO/CategoriaDAO.php';

class categoriaHelper
{
    private $categoriaDAO;

    public function __construct()
    {
        try {
            $db = (new Database())->getConnection();
            $this->categoriaDAO = new CategoriaDAO($db);
        } catch (Exception $e) {
            error_log("Error fatal en addminAssociadoHelper al conectar a BD: " . $e->getMessage());
        }
    }

    public function obtenerCategoriasDelEmprendimiento($idEmprendimiento)
    {
        try {
            return $this->categoriaDAO->trearCategorias($idEmprendimiento);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return null;
        }
    }
}
