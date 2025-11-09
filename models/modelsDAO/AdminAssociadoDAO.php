<?php

require_once "../models/modelsDAO/UsuarioDAO.php";

class AdminAssociadoDAO
{
    private $conn;
    private $usuarioDAO;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->usuarioDAO = new UsuarioDAO($db);
    }

    public function registrar(AdminAssociado $admin)
    {
        $idUsuario = $this->usuarioDAO->registrar($admin);

        $sql = "INSERT INTO adminassociado(adminAssociado_idUsuario, apellido, descripcion, fotoPerfil)VALUES(?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $idUsuario,
            $admin->getApellido(),
            $admin->getDescripcion(),
            $admin->getFotoPerfil(),
        ]);

        return $idUsuario;
    }

    public function obterIdPorEmail($email)
    {
        $sql = "SELECT u.idUsuario FROM usuario u 
                INNER JOIN adminassociado a ON u.idUsuario = a.adminAssociado_idUsuario 
                WHERE u.email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetchColumn();
    }

    public function obtenerPorId($id)
    {
        $sql = "SELECT u.nombre as NombreAssociado, a.apellido as ApellidoAssociado, a.descripcion as DescripcionAssociado, a.fotoPerfil as FotoPerfilAssociado FROM usuario u 
                INNER JOIN adminassociado a ON u.idUsuario = a.adminAssociado_idUsuario 
                WHERE u.idUsuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarPerfil($idUsuario, $dados)
    {
        try {
            $fields = [
                'apellido = ?',
                'descripcion = ?'
            ];
            $params = [
                $dados['apellido'],
                $dados['descripcion']
            ];

            if (isset($dados['fotoPerfil'])) {
                $fields[] = 'fotoPerfil = ?';
                $params[] = $dados['fotoPerfil'];
            }

            $params[] = $idUsuario;

            $sql = "UPDATE adminassociado SET " . implode(', ', $fields) . " WHERE adminAssociado_idUsuario = ?";

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erro em AdminAssociadoDAO::atualizarPerfil: " . $e->getMessage());
            return false;
        }
    }
}
