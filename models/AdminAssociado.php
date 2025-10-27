<?php

class AdminAssociado extends Usuario{
    private $apellido;
    private $descripcion;
    private $fotoPerfil;

    public function __construct($nombre, $email, $password, $tipo, $apellido, $descripcion, $fotoPerfil){
        parent::__construct($nombre, $email, $password, $tipo);
        $this->apellido = $apellido;
        $this->descripcion = $descripcion;
        $this->fotoPerfil = $fotoPerfil;
    }

    // Getters
    public function getApellido() { return $this->apellido; }
    public function getDescripcion() { return $this->descripcion; }
    public function getFotoPerfil() { return $this->fotoPerfil; }

}

?>