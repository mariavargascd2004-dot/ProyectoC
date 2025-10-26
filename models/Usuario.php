<?php

class Usuario
{
    private $idUsuario;
    private $nombre;
    private $email;
    private $password;
    private $tipo;

    public function __construct($nombre, $email, $password, $tipo)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->tipo = $tipo;
    }

    //GETTER
    public function getNombre()
    {
        return $this->nombre;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getTipo()
    {
        return $this->tipo;
    }

    //SETTERS
}
