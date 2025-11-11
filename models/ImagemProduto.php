<?php
class ImagemProduto
{
    private $id;
    private $produto_id;
    private $caminhoImagem;
    private $ordem;

    public function __construct($produto_id, $caminhoImagem, $ordem = 0)
    {
        $this->produto_id = $produto_id;
        $this->caminhoImagem = $caminhoImagem;
        $this->ordem = $ordem;
    }

    // GETTERS
    public function getId()
    {
        return $this->id;
    }
    public function getProduto_id()
    {
        return $this->produto_id;
    }
    public function getCaminhoImagem()
    {
        return $this->caminhoImagem;
    }
    public function getOrdem()
    {
        return $this->ordem;
    }

    // SETTERS
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }
}
