<?php

class AdminAssociadoDAO{
    private $conn;
    private $usuarioDAO;

    public function __construct($db)
    {
        $this->conn = $db;
        $this->usuarioDAO = new UsuarioDAO($db);
    }

    public function registrar(AdminAssociado $admin){
        $idUsuario = $this->usuarioDAO->registrar($admin);

        $sql = "INSERT INTO adminassociado(adminAssociado_idUsuario, apellido, descripcion, fotoPerfil, aprobado)VALUES(?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $idUsuario,
            $admin->getApellido(),
            $admin->getDescripcion(),
            $admin->getFotoPerfil(),
            $admin->getAprobado()
        ]);

        return $idUsuario;
    }
}

?>