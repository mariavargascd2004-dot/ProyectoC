<?php
require_once "../models/ImagemFabricacao.php";

class ImagemFabricacaoDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function inserir(ImagemFabricacao $imagem)
    {
        $sql = "INSERT INTO imagem_fabricacao (emprendimento_id, caminho_imagem, ordem) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $imagem->getEmprendimentoId(),
            $imagem->getCaminhoImagem(),
            $imagem->getOrdem()
        ]);
    }

    public function listarPorEmprendimento($emprendimentoId)
    {
        $sql = "SELECT * FROM imagem_fabricacao WHERE emprendimento_id = ? ORDER BY ordem";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$emprendimentoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function excluirPorEmprendimento($emprendimentoId)
    {
        $sql = "DELETE FROM imagem_fabricacao WHERE emprendimento_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$emprendimentoId]);
    }

    public function contarPorEmprendimento($emprendimentoId)
    {
        $sql = "SELECT COUNT(*) FROM imagem_fabricacao WHERE emprendimento_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$emprendimentoId]);
        return $stmt->fetchColumn();
    }
}
