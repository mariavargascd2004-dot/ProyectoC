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
        // Primero obtenemos los emprendimientos sin imágenes
        $sql = "SELECT 
            e.*,
            u.idUsuario AS idAdmin, 
            u.nombre AS nomeAdmin, 
            u.email AS emailAdmin,
            a.apellido AS adminApellido, 
            a.descripcion AS adminDescripcion
        FROM emprendimento e
        INNER JOIN usuario u 
            ON e.adminAssociado_idUsuario = u.idUsuario
        INNER JOIN adminassociado a
            ON a.adminAssociado_idUsuario = u.idUsuario
        WHERE e.aprovado = 0
        ORDER BY e.idEmprendimento DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $emprendimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Luego obtenemos las imágenes por separado
        if (!empty($emprendimientos)) {
            foreach ($emprendimientos as &$emp) {
                // Imágenes de galería
                $sqlGaleria = "SELECT caminho_imagem FROM imagem_galeria 
                          WHERE emprendimento_id = ? ORDER BY ordem";
                $stmtGaleria = $this->conn->prepare($sqlGaleria);
                $stmtGaleria->execute([$emp['idEmprendimento']]);
                $emp['galeria_imagens'] = $stmtGaleria->fetchAll(PDO::FETCH_COLUMN, 0);

                // Imágenes de fabricación
                $sqlFabricacao = "SELECT caminho_imagem FROM imagem_fabricacao 
                             WHERE emprendimento_id = ? ORDER BY ordem";
                $stmtFabricacao = $this->conn->prepare($sqlFabricacao);
                $stmtFabricacao->execute([$emp['idEmprendimento']]);
                $emp['fabricacao_imagens'] = $stmtFabricacao->fetchAll(PDO::FETCH_COLUMN, 0);
            }
        }

        return $emprendimientos;
    }

    public function atualizarAprovacao($idEmprendimento, $aprovado)
    {
        $sql = "UPDATE emprendimento SET aprovado = ? WHERE idEmprendimento = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$aprovado, $idEmprendimento]);
    }
}
