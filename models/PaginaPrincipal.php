<?php

class PaginaPrincipal
{
    private $portada;
    private $historia;
    private $mision;
    private $vision;
    private $primerafotogaleria;
    private $segundafotogaleria;
    private $tercerafotogaleria;
    private $cuartafotogaleria;
    private $telefono;
    private $direccion;
    private $horarios;
    private $celular;
    private $facebook;
    private $instagram;
    // Nuevo campo
    private $logo; 

    public function __construct($portada, $historia, $mision, $vision, $primerafotogaleria, $segundafotogaleria, $tercerafotogaleria, $cuartafotogaleria, $telefono, $direccion, $horarios, $celular, $facebook, $instagram, $logo)
    {
        $this->portada = $portada;
        $this->historia = $historia;
        $this->mision = $mision;
        $this->vision = $vision;
        $this->primerafotogaleria = $primerafotogaleria;
        $this->segundafotogaleria = $segundafotogaleria;
        $this->tercerafotogaleria = $tercerafotogaleria;
        $this->cuartafotogaleria = $cuartafotogaleria;
        $this->telefono = $telefono;
        $this->direccion = $direccion;
        $this->horarios = $horarios;
        $this->celular = $celular;
        $this->facebook = $facebook;
        $this->instagram = $instagram;
        $this->logo = $logo;
    }

    // GETTERS
    public function getPortada()
    {
        return $this->portada;
    }

    public function getHistoria()
    {
        return $this->historia;
    }

    public function getMision()
    {
        return $this->mision;
    }

    public function getVision()
    {
        return $this->vision;
    }

    public function getPrimerafotogaleria()
    {
        return $this->primerafotogaleria;
    }

    public function getSegundafotogaleria()
    {
        return $this->segundafotogaleria;
    }

    public function getTercerafotogaleria()
    {
        return $this->tercerafotogaleria;
    }

    public function getCuartafotogaleria()
    {
        return $this->cuartafotogaleria;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getHorarios()
    {
        return $this->horarios;
    }

    public function getCelular()
    {
        return $this->celular;
    }

    public function getFacebook()
    {
        return $this->facebook;
    }

    public function getInstagram()
    {
        return $this->instagram;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    // SETTERS
    public function setPordata($portata)
    {
        $this->portada = $portata;
    }

    public function setHistoria($historia)
    {
        $this->historia = $historia;
    }

    public function setMision($mision)
    {
        $this->mision = $mision;
    }

    public function setVision($vision)
    {
        $this->vision = $vision;
    }

    public function setPrimerafotogaleria($primerafotogaleria)
    {
        $this->primerafotogaleria = $primerafotogaleria;
    }

    public function setSegundafotogaleria($segundafotogaleria)
    {
        $this->segundafotogaleria = $segundafotogaleria;
    }

    public function setTercerafotogaleria($tercerafotogaleria)
    {
        $this->tercerafotogaleria = $tercerafotogaleria;
    }

    public function setCuartafotogaleria($cuartafotogaleria)
    {
        $this->cuartafotogaleria = $cuartafotogaleria;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    }

    public function setHorarios($horarios)
    {
        $this->horarios = $horarios;
    }

    public function setCelular($celular)
    {
        $this->celular = $celular;
    }

    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    public function setInstagram($instagram)
    {
        $this->instagram = $instagram;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
    }
}