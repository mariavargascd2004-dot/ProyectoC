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
}
