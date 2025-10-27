<?php
class ImagemGaleria
{
    private $idImagem;
    private $emprendimentoId;
    private $caminhoImagem;
    private $ordem;

    public function __construct($emprendimentoId, $caminhoImagem, $ordem = 0)
    {
        $this->emprendimentoId = $emprendimentoId;
        $this->caminhoImagem = $caminhoImagem;
        $this->ordem = $ordem;
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
  

    // SETTERS
    public function setOrdem($ordem)
    {
        $this->ordem = $ordem;
    }
}
