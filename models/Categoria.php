<?php
class Categoria{
    private $idCategoria;
    private $idEmprendimiento;
    private $nombre;

    public function __construct($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getIdCategoria(){
        return $this->idCategoria;
    }
    public function getIdEmprendimiento(){
        return $this->idEmprendimiento;
    }
    public function getNombre(){
        return $this->nombre;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }
}
?>