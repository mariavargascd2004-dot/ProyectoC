<?php
require_once "../models/AdminGeneral.php";

class AdminGeneralDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //Funcion para Login
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
}
