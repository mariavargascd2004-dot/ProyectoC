<?php
require_once '../config/database.php';
require_once '../models/modelsDAO/ProdutoDAO.php';

class produtoHelper
{
    private $produtoDAO;

    public function __construct()
    {
        try {
            $db = (new Database())->getConnection();
            $this->produtoDAO = new ProdutoDAO($db);
        } catch (Exception $e) {
            error_log("Error fatal en addminAssociadoHelper al conectar a BD: " . $e->getMessage());
        }
    }

    public function obtenerProductosDelEmprendimiento($idEmprendimiento)
    {
        try {
            return $this->produtoDAO->traerProductosDelEmprendimiento($idEmprendimiento);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return null;
        }
    }
}
