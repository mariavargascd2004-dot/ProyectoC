<?php
require_once "../models/ImagemProduto.php";

class ImagemProdutoDAO
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

   public function inserir(ImagemProduto $imagem)
    {
        $sql = "INSERT INTO imagem_produto (produto_id, caminho_imagem, ordem) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            $imagem->getProduto_id(),
            $imagem->getCaminhoImagem(),
            $imagem->getOrdem()
        ]);
        return $this->conn->lastInsertId();
    }

    public function obterProximaOrdemDisponivel($produtoId)
    {
        $sql = "SELECT ordem FROM imagem_produto WHERE produto_id = ? ORDER BY ordem ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$produtoId]);

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

    public function listarPorProduto($produtoId)
    {
        $sql = "SELECT * FROM imagem_produto WHERE produto_id = ? ORDER BY ordem";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$produtoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function excluirPorProduto($produtoId)
    {
        $sql = "DELETE FROM imagem_produto WHERE produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$produtoId]);
    }

    public function contarPorProduto($produtoId)
    {
        $sql = "SELECT COUNT(*) FROM imagem_produto WHERE produto_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$produtoId]);
        return $stmt->fetchColumn();
    }

    public function obterPorId($idImagem)
    {
        $sql = "SELECT * FROM imagem_produto WHERE idImagem = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$idImagem]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function excluirPorId($idImagem)
    {
        $sql = "DELETE FROM imagem_produto WHERE idImagem = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$idImagem]);
    }
}
