<?php
require_once "../models/SubCategoria.php";

class SubCateogriaDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function trearSubCategorias($idCategoria)
    {
        $sql = "SELECT * FROM subcategoria WHERE categoria_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idCategoria]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obterPorId($idSubcategoria)
    {
        $sql = "SELECT * FROM subcategoria WHERE idSubcategoria = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idSubcategoria]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function contarPorCategoria($idCategoria)
    {
        $sql = "SELECT COUNT(*) FROM subcategoria WHERE categoria_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idCategoria]);
        return $stmt->fetchColumn();
    }

    public function inserir($nome, $idCategoria)
    {
        $sql = "INSERT INTO subcategoria (nombre, categoria_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute([$nome, $idCategoria])) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function atualizar($idSubcategoria, $novoNome)
    {
        $sql = "UPDATE subcategoria SET nombre = ? WHERE idSubcategoria = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$novoNome, $idSubcategoria]);
    }

    public function excluir($idSubcategoria)
    {
        $sql = "DELETE FROM subcategoria WHERE idSubcategoria = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$idSubcategoria]);
    }
}
