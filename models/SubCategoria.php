<?php
class Subcategoria{
    private $idSubcategoria;
    private $nombre;

    public function __construct($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getIdSubcategoria(){
        return $this->idSubcategoria;
    }
    public function getNombre(){
        return $this->nombre;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }
}
?>