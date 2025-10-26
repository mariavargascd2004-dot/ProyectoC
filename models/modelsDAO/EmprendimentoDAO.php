<?php

class EmprendimentoDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function registrar(Emprendimento $emprendimento)
    {
        $sql = "INSERT INTO emprendimento 
                (adminAssociado_idUsuario, nome, logo, historia, processoFabricacao, telefone, celular, ubicacao, instagram, facebook, aprovado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $emprendimento->getAdminAssociadoIdUsuario(),
            $emprendimento->getNome(),
            $emprendimento->getLogo(),
            $emprendimento->getHistoria(),
            $emprendimento->getProcessoFabricacao(),
            $emprendimento->getTelefone(),
            $emprendimento->getCelular(),
            $emprendimento->getUbicacao(),
            $emprendimento->getInstagram(),
            $emprendimento->getFacebook(),
            $emprendimento->getAprovado()
        ]);

        return $this->conn->lastInsertId();
    }

    public function existeNome($nome)
    {
        $sql = "SELECT COUNT(*) FROM emprendimento WHERE nome = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$nome]);
        return $stmt->fetchColumn() > 0;
    }

    public function obterPorId($idEmprendimento)
    {
        $sql = "SELECT * FROM emprendimento WHERE idEmprendimento = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idEmprendimento]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTodosComAdmin()
    {
        $sql = "SELECT e.*, 
                   u.idUsuario AS idAdmin, 
                   u.nombre AS nomeAdmin, 
                   u.email AS emailAdmin
            FROM emprendimento e
            INNER JOIN usuario u 
                ON e.adminAssociado_idUsuario = u.idUsuario
            ORDER BY e.idEmprendimento DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $resultados ?: null;
    }

    public function listarNoAprovados()
    {
        $sql = "SELECT e.*, u.nombre as nomeAdmin 
                FROM emprendimento e 
                INNER JOIN usuario u ON e.adminAssociado_idUsuario = u.idUsuario 
                WHERE e.aprovado = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarAprovacao($idEmprendimento, $aprovado)
    {
        $sql = "UPDATE emprendimento SET aprovado = ? WHERE idEmprendimento = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$aprovado, $idEmprendimento]);
    }
}
