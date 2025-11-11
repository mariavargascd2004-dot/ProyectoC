<?php

class Evento
{
    private $idEvento;
    private $titulo;
    private $descripcion;
    private $fechaInicio;
    private $fechaFinal;
    private $ubicacion;
    private $estado;
    private $imagen;

    public function __construct($idEvento, $titulo, $descripcion, $fechaInicio, $fechaFinal, $ubicacion, $estado, $imagen)
    {
        $this->idEvento = $idEvento;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFinal = $fechaFinal;
        $this->ubicacion = $ubicacion;
        $this->estado = $estado;
        $this->imagen = $imagen;
    }

    // GETTERS
    public function getIdEvento()
    {
        return $this->idEvento;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function getDescricao() 
    {
        return $this->descripcion;
    }

    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    public function getFechaFinal()
    {
        return $this->fechaFinal;
    }
    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getImagen()
    {
        return $this->imagen;
    }

    // SETTERS
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function setDescricao($descripcion) 
    {
        $this->descripcion = $descripcion;
    }

    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    }

    public function setFechaFinal($fechaFinal)
    {
        $this->fechaFinal = $fechaFinal;
    }
    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }

    public function toArray()
    {
        return [
            'idEvento' => $this->idEvento,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'fechaInicio' => $this->fechaInicio,
            'fechaFinal' => $this->fechaFinal,
            'ubicacion' => $this->ubicacion,
            'estado' => $this->estado,
            'imagen' => $this->imagen
        ];
    }
}
