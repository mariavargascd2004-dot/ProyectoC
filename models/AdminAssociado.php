<?php

class AdminAssociado extends Usuario{
    private $apellido;
    private $descripcion;
    private $fotoPerfil;
    private $aprobado;

    public function __construct($nombre, $email, $password, $tipo, $apellido, $descripcion, $fotoPerfil, $aprobado){
        parent::__construct($nombre, $email, $password, $tipo);
        $this->apellido = $apellido;
        $this->descripcion = $descripcion;
        $this->fotoPerfil = $fotoPerfil;
        $this->aprobado = $aprobado;
    }

    // Getters
    public function getApellido() { return $this->apellido; }
    public function getDescripcion() { return $this->descripcion; }
    public function getFotoPerfil() { return $this->fotoPerfil; }
    public function getAprobado() { return $this->aprobado; }

}

?>