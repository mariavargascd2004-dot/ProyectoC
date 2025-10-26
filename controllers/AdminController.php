<?php
session_start();
require_once "../config/database.php";
require_once "../models/AdminGeneral.php";
require_once "../models/modelsDAO/AdminGeneralDAO.php";

$db  = (new Database())->getConnection();
$adminGeneralDAO = new AdminGeneralDAO($db);
$adminTipo = "adminGeneral";

$accion = $_POST["accion"] ?? "";

switch ($accion) {
    case 'login':
        $usuario = $_POST['usuario']; 
        $password = $_POST['password'];

        $user = $adminGeneralDAO->login($usuario, $password);
        if ($user) {
            $_SESSION["user"] = [
                "id" => $user['idAdminGeneral'],
                "nome" => $user['nombre'],
                "tipo" => $adminTipo,
                "alerta" => true,
            ];
            echo json_encode(['status' => 'ok', "message" => "Bem vindo/a de novo Administrador!", "tipo" => $adminTipo]);
        } else {

            echo json_encode(['status' => 'error', 'message' => 'Credenciales incorrectas']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
}
