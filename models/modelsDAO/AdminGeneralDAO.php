<?php
require_once "../models/AdminGeneral.php";

class AdminGeneralDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Funcion para Login
    public function login($usuario, $password)
    {
        $sql = "SELECT * FROM admingeneral WHERE usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }

    
    public function actualizarPassword($idAdmin, $password)
    {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE admingeneral SET password = ? WHERE idAdminGeneral = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$hash, $idAdmin]);
        } catch (PDOException $e) {
            error_log("Erro em AdminGeneralDAO::actualizarPassword: " . $e->getMessage());
            return false;
        }
    }
}