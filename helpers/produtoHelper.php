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

    public function obtenerProductosFiltrados($filtros = [])
    {
        try {
            return $this->produtoDAO->obtenerProductosFiltrados($filtros);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerColoresDisponibles($idEmprendimiento = null)
    {
        try {
            return $this->produtoDAO->obtenerColoresDisponibles($idEmprendimiento);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerTamanosDisponibles($idEmprendimiento = null)
    {
        try {
            return $this->produtoDAO->obtenerTamanosDisponibles($idEmprendimiento);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return [];
        }
    }
}
