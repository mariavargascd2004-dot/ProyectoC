<?php

class AdminGeneral
{
    private $idAdminGeneral;
    private $usuario;
    private $password;

    public function __construct($usuario, $password)
    {
        $this->usuario = $usuario;
        $this->password = $password;
    }

    //GETTERS
    public function getUsuario()
    {
        return $this->usuario;
    }
    public function getPassword()
    {
        return $this->password;
    }
}
