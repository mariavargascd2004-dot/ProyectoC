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

    public function obterPorAdmin($adminAssociado_idUsuario)
    {
        $sql = "SELECT * FROM emprendimento WHERE adminAssociado_idUsuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$adminAssociado_idUsuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public function listarAprovados()
    {
        $sql = "SELECT e.*, u.nome as nomeAdmin 
                FROM emprendimento e 
                INNER JOIN usuario u ON e.adminAssociado_idUsuario = u.idUsuario 
                WHERE e.aprovado = 1";
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
