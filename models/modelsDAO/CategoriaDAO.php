<?php
require_once "../models/Categoria.php";

class CategoriaDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function trearCategorias($idEmprendimiento)
    {
        $sql = "SELECT * FROM categoria WHERE emprendimiento_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idEmprendimiento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterPorId($idCategoria)
    {
        $sql = "SELECT * FROM categoria WHERE idCategoria = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idCategoria]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function inserir($nome, $idEmprendimento)
    {
        $sql = "INSERT INTO categoria (nombre, emprendimiento_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute([$nome, $idEmprendimento])) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function atualizar($idCategoria, $novoNome)
    {
        $sql = "UPDATE categoria SET nombre = ? WHERE idCategoria = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$novoNome, $idCategoria]);
    }

    public function excluir($idCategoria)
    {
        $sql = "DELETE FROM categoria WHERE idCategoria = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$idCategoria]);
    }
}
