<?php
require_once "../models/Usuario.php";

class UsuarioDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Función para registrar Usuario Cliente
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

    // Funcion para Login
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

    public function obterTipoPorId($idUsuario)
    {
        $sql = "SELECT tipo FROM usuario WHERE idUsuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idUsuario]);
        return $stmt->fetchColumn();
    }

    // Verificar si existe el Email
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

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function atualizarNome($idUsuario, $nombre)
    {
        try {
            $sql = "UPDATE usuario SET nombre = ? WHERE idUsuario = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$nombre, $idUsuario]);
        } catch (PDOException $e) {
            error_log("Erro em UsuarioDAO::atualizarNome: " . $e->getMessage());
            return false;
        }
    }

    // --- NUEVA FUNCIÓN PARA ACTUALIZAR PASSWORD ---
    public function actualizarPassword($idUsuario, $password)
    {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuario SET password = ? WHERE idUsuario = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$hash, $idUsuario]);
        } catch (PDOException $e) {
            error_log("Erro em UsuarioDAO::actualizarPassword: " . $e->getMessage());
            return false;
        }
    }
}