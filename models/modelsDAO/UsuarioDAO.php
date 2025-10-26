<?php
require_once "../models/Usuario.php";

class UsuarioDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //Función para registrar Usuario Cliente
    public function registrar(Usuario $usuario)
    {
        $sql = "INSERT INTO usuario(nombre, email, password, tipo)VALUES(?, ?, ?, ?);";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $usuario->getNombre(),
            $usuario->getEmail(),
            password_hash($usuario->getPassword(), PASSWORD_DEFAULT),
            $usuario->getTipo()
        ]);
        return $this->conn->lastInsertId();
    }


    //Funcion para Login
    public function login($email, $password)
    {
        $sql = "SELECT * FROM usuario WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }

    //Verificar si existe el Email
    public function existeEmail($email)
    {
        $sql = "SELECT idUsuario FROM usuario WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    public function eliminarUsuario($id)
    {
        $sql = "DELETE FROM usuario WHERE idUsuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        // Verifica si alguna fila fue eliminada
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
