<?php

class Produto{

    private $titulo;
    private $emprendimiento_id;
    private $producto_idCategoria;
    private $producto_idSubcategoria;
    private $descripcion;
    private $tamano;
    private $color;
    private $fechaAgregado;

    public function __construct($titulo, $emprendimiento_id, $producto_idCategoria, $producto_idSubcategoria, $descripcion, $tamano, $color, $fechaAgregado)
    {
        $this->titulo = $titulo;
        $this->emprendimiento_id = $emprendimiento_id;
        $this->producto_idCategoria = $producto_idCategoria;
        $this->producto_idSubcategoria = $producto_idSubcategoria;
        $this->descripcion = $descripcion;
        $this->tamano = $tamano;
        $this->color = $color;
        $this->fechaAgregado = $fechaAgregado;
    }

    public function getTitulo(){
        return $this->titulo;
    }
    public function getEmprendimiento_id(){
        return $this->emprendimiento_id;
    }
    public function getProducto_idCategoria(){
        return $this->producto_idCategoria;
    }
    public function getProducto_idSubcategoria(){
        return $this->producto_idSubcategoria;
    }
    public function getDescripcion(){
        return $this->descripcion;
    }
    public function getTamano(){
        return $this->tamano;
    }
    public function getColor(){
        return $this->color;
    }
    public function getFechaAgregado(){
        return $this->fechaAgregado;
    }

}

?>