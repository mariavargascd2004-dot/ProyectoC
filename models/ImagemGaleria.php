<?php
class ImagemGaleria
{
    private $idImagem;
    private $emprendimentoId;
    private $caminhoImagem;
    private $ordem;
    private $legenda;

    public function __construct($emprendimentoId, $caminhoImagem, $ordem = 0, $legenda = null)
    {
        $this->emprendimentoId = $emprendimentoId;
        $this->caminhoImagem = $caminhoImagem;
        $this->ordem = $ordem;
        $this->legenda = $legenda;
    }

    // GETTERS
    public function getIdImagem()
    {
        return $this->idImagem;
    }
    public function getEmprendimentoId()
    {
        return $this->emprendimentoId;
    }
    public function getCaminhoImagem()
    {
        return $this->caminhoImagem;
    }
    public function getOrdem()
    {
        return $this->ordem;
    }
    public function getLegenda()
    {
        return $this->legenda;
    }

    // SETTERS
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }
    public function setLegenda($legenda)
    {
        $this->legenda = $legenda;
    }
}
