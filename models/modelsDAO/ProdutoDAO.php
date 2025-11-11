<?php
require_once "../models/Produto.php";

class ProdutoDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function traerProductosDelEmprendimiento($idEmprendimiento)
    {
        $sql = "SELECT * FROM productos WHERE emprendimiento_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idEmprendimiento]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardarProduto($dados)
    {
        $sql = "INSERT INTO productos 
            (titulo, emprendimiento_id, producto_idCategoria, producto_idSubcategoria, descripcion, tamano, color, precio, fechaAgregado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($sql);
        $ok = $stmt->execute([
            $dados["titulo"],
            $dados["emprendimiento_id"],
            $dados["producto_idCategoria"],
            $dados["producto_idSubcategoria"],
            $dados["descripcion"],
            $dados["tamano"],
            $dados["color"],
            $dados["precio"]
        ]);

        if ($ok) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    public function obtenerProductoPorId($idProducto)
    {
        $sql = "SELECT * FROM productos WHERE idProducto = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idProducto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarProducto($idProducto, $dados)
    {
        $sql = "UPDATE productos SET 
                titulo = ?, 
                producto_idCategoria = ?, 
                producto_idSubcategoria = ?, 
                descripcion = ?, 
                tamano = ?, 
                color = ?, 
                precio = ?
            WHERE idProducto = ?";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $dados["titulo"],
            $dados["producto_idCategoria"],
            $dados["producto_idSubcategoria"],
            $dados["descripcion"],
            $dados["tamano"],
            $dados["color"],
            $dados["precio"],
            $idProducto
        ]);
    }

    public function contarPorCategoria($idCategoria)
    {
        $sql = "SELECT COUNT(*) FROM productos WHERE producto_idCategoria = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idCategoria]);
        return $stmt->fetchColumn();
    }

    public function contarPorSubcategoria($idSubcategoria)
    {
        $sql = "SELECT COUNT(*) FROM productos WHERE producto_idSubcategoria = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idSubcategoria]);
        return $stmt->fetchColumn();
    }
}
