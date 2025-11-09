<?php
require_once "../models/ImagemGaleria.php";

class ImagemGaleriaDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function inserir(ImagemGaleria $imagem)
    {
        $sql = "INSERT INTO imagem_galeria (emprendimento_id, caminho_imagem, ordem) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $imagem->getEmprendimentoId(),
            $imagem->getCaminhoImagem(),
            $imagem->getOrdem(),
        ]);
        return $this->conn->lastInsertId();
    }

    public function obterProximaOrdemDisponivel($emprendimentoId)
    {
        $sql = "SELECT ordem FROM imagem_galeria WHERE emprendimento_id = ? ORDER BY ordem ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$emprendimentoId]);

        $ordens = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $ordens = array_map('intval', $ordens);

        $ordemEsperada = 0;

        foreach ($ordens as $ordem) {
            if ($ordem != $ordemEsperada) {
                return $ordemEsperada;
            }
            $ordemEsperada++;
        }

        return $ordemEsperada;
    }

    public function listarPorEmprendimento($emprendimentoId)
    {
        $sql = "SELECT * FROM imagem_galeria WHERE emprendimento_id = ? ORDER BY ordem";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$emprendimentoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function excluirPorEmprendimento($emprendimentoId)
    {
        $sql = "DELETE FROM imagem_galeria WHERE emprendimento_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$emprendimentoId]);
    }

    public function contarPorEmprendimento($emprendimentoId)
    {
        $sql = "SELECT COUNT(*) FROM imagem_galeria WHERE emprendimento_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$emprendimentoId]);
        return $stmt->fetchColumn();
    }

    public function obterPorId($idImagem)
    {
        $sql = "SELECT * FROM imagem_galeria WHERE idImagem = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idImagem]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function excluirPorId($idImagem)
    {
        $sql = "DELETE FROM imagem_galeria WHERE idImagem = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$idImagem]);
    }
}
