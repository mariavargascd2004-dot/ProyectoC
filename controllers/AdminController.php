<?php
require_once "../config/database.php";
require_once "../models/AdminGeneral.php";
require_once "../models/AdminGeneralDAO.php";

$db  = (new Database())->getConnection();
$adminGeneralDAO = new AdminGeneralDAO($db);

$accion = $_POST["accion"] ?? "";

switch ($accion) {
    case 'login':
        $usuario = $_POST['usuario']; 
        $password = $_POST['password'];

        $user = $adminGeneralDAO->login($usuario, $password);
        if ($user) {
            $_SESSION['user'] = [
                "id" => $user['idAdminGeneral'],
                "tipo" => "adminGeneral"
            ];
            echo json_encode(['status' => 'ok', "message" => "Bem vindo/a de novo Administrador!"]);
        } else {

            echo json_encode(['status' => 'error', 'message' => 'Credenciales incorrectas']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
}
