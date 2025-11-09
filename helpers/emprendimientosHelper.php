<?php
require_once '../config/database.php';
require_once '../models/modelsDAO/EmprendimentoDAO.php';

class EmprendimientosHelper
{
    private $emprendimentoDAO;

    public function __construct()
    {
        try {
            $db = (new Database())->getConnection();
            $this->emprendimentoDAO = new EmprendimentoDAO($db);
        } catch (Exception $e) {
            error_log("Error fatal en EmprendimientosHelper al conectar a BD: " . $e->getMessage());
        }
    }

    public function obtenerEmprendimientosAprobados()
    {
        try {
            $emprendimentos = $this->emprendimentoDAO->listarTodosComAdmin();

            if (!$emprendimentos) {
                return [];
            }

            $aprobados = array_filter($emprendimentos, function ($emp) {
                return $emp['aprovado'] == 1;
            });

            return array_values($aprobados);
        } catch (Exception $e) {
            error_log("Error obteniendo emprendimientos aprobados: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerEmprendimientosPendientes()
    {
        try {
            return $this->emprendimentoDAO->listarNoAprovados();
        } catch (Exception $e) {
            error_log("Error obteniendo emprendimientos pendientes: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerEmprendimientoPorId($id)
    {
        try {
            return $this->emprendimentoDAO->obterPorId($id);
        } catch (Exception $e) {
            error_log("Error buscando emprendimiento $id: " . $e->getMessage());
            return null;
        }
    }
}
