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

    public function eliminarProducto($idProducto)
    {
        $sql = "DELETE FROM productos WHERE idProducto = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$idProducto]);
    }

    public function obtenerProductosFiltrados($filtros = [])
    {
        $sql = "SELECT p.*, e.nome as emprendimiento_nome FROM productos p 
                LEFT JOIN emprendimento e ON p.emprendimiento_id = e.idEmprendimento 
                LEFT JOIN categoria c ON p.producto_idCategoria = c.idCategoria
                LEFT JOIN subcategoria s ON p.producto_idSubcategoria = s.idSubcategoria
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['idEmprendimiento'])) {
            $sql .= " AND p.emprendimiento_id = ?";
            $params[] = $filtros['idEmprendimiento'];
        }

        if (!empty($filtros['q'])) {
            $sql .= " AND (p.titulo LIKE ? OR p.descripcion LIKE ?)";
            $params[] = "%" . $filtros['q'] . "%";
            $params[] = "%" . $filtros['q'] . "%";
        }

        if (!empty($filtros['categorias'])) {
            $inQuery = implode(',', array_fill(0, count($filtros['categorias']), '?'));
            $sql .= " AND c.nombre IN ($inQuery)";
            $params = array_merge($params, $filtros['categorias']);
        }

        if (!empty($filtros['subcategorias'])) {
            $inQuery = implode(',', array_fill(0, count($filtros['subcategorias']), '?'));
            $sql .= " AND s.nombre IN ($inQuery)";
            $params = array_merge($params, $filtros['subcategorias']);
        }

        if (!empty($filtros['cor'])) {
            $inQuery = implode(',', array_fill(0, count($filtros['cor']), '?'));
            $sql .= " AND p.color IN ($inQuery)";
            $params = array_merge($params, $filtros['cor']);
        }

        if (!empty($filtros['tamanho'])) {
            $inQuery = implode(',', array_fill(0, count($filtros['tamanho']), '?'));
            $sql .= " AND p.tamano IN ($inQuery)";
            $params = array_merge($params, $filtros['tamanho']);
        }

        if (isset($filtros['min_price']) && is_numeric($filtros['min_price'])) {
            $sql .= " AND p.precio >= ?";
            $params[] = $filtros['min_price'];
        }

        if (isset($filtros['max_price']) && is_numeric($filtros['max_price'])) {
            $sql .= " AND p.precio <= ?";
            $params[] = $filtros['max_price'];
        }

        if (!empty($filtros['sort'])) {
            switch ($filtros['sort']) {
                case 'price_asc':
                    $sql .= " ORDER BY p.precio ASC";
                    break;
                case 'price_desc':
                    $sql .= " ORDER BY p.precio DESC";
                    break;
                case 'newest':
                default:
                    $sql .= " ORDER BY p.fechaAgregado DESC";
                    break;
            }
        } else {
            $sql .= " ORDER BY p.fechaAgregado DESC";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerColoresDisponibles($idEmprendimiento = null)
    {
        $sql = "SELECT DISTINCT color FROM productos WHERE color IS NOT NULL AND color != ''";
        $params = [];
        if ($idEmprendimiento) {
            $sql .= " AND emprendimiento_id = ?";
            $params[] = $idEmprendimiento;
        }
        $sql .= " ORDER BY color ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function obtenerTamanosDisponibles($idEmprendimiento = null)
    {
        $sql = "SELECT DISTINCT tamano FROM productos WHERE tamano IS NOT NULL AND tamano != ''";
        $params = [];
        if ($idEmprendimiento) {
            $sql .= " AND emprendimiento_id = ?";
            $params[] = $idEmprendimiento;
        }
        $sql .= " ORDER BY tamano ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
