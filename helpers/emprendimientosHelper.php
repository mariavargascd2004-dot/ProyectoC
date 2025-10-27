<?php
function obtenerEmprendimientosAprobados()
{
    require_once '../config/database.php';
    require_once '../models/modelsDAO/EmprendimentoDAO.php';

    try {
        $db = (new Database())->getConnection();
        $emprendimentoDAO = new EmprendimentoDAO($db);
        $emprendimentos = $emprendimentoDAO->listarTodosComAdmin();

        $aprobados = [];
        if ($emprendimentos) {
            foreach ($emprendimentos as $emp) {
                if ($emp['aprovado'] == 1) {
                    $aprobados[] = $emp;
                }
            }
        }
        return $aprobados;
    } catch (Exception $e) {
        error_log("Error obteniendo emprendimientos: " . $e->getMessage());
        return [];
    }
}

function obtenerEmprendimientosPendientes()
{
    require_once '../config/database.php';
    require_once '../models/modelsDAO/EmprendimentoDAO.php';

    try {
        $db = (new Database())->getConnection();
        $emprendimentoDAO = new EmprendimentoDAO($db);
        return $emprendimentoDAO->listarNoAprovados();
    } catch (Exception $e) {
        error_log("Error obteniendo emprendimientos pendientes: " . $e->getMessage());
        return [];
    }
}
