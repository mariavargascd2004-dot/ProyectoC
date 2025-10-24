<?php

class Emprendimento
{
    private $idEmprendimento;
    private $adminAssociado_idUsuario;
    private $nome;
    private $logo;
    private $historia;
    private $processoFabricacao;
    private $telefone;
    private $celular;
    private $ubicacao;
    private $instagram;
    private $facebook;
    private $aprovado;

    public function __construct($adminAssociado_idUsuario, $nome, $historia, $processoFabricacao, $celular, $ubicacao)
    {
        $this->adminAssociado_idUsuario = $adminAssociado_idUsuario;
        $this->nome = $nome;
        $this->historia = $historia;
        $this->processoFabricacao = $processoFabricacao;
        $this->celular = $celular;
        $this->ubicacao = $ubicacao;
        $this->aprovado = 0;
    }

    // GETTERS
    public function getIdEmprendimento()
    {
        return $this->idEmprendimento;
    }
    public function getAdminAssociadoIdUsuario()
    {
        return $this->adminAssociado_idUsuario;
    }
    public function getNome()
    {
        return $this->nome;
    }
    public function getLogo()
    {
        return $this->logo;
    }
    public function getHistoria()
    {
        return $this->historia;
    }
    public function getProcessoFabricacao()
    {
        return $this->processoFabricacao;
    }
    public function getTelefone()
    {
        return $this->telefone;
    }
    public function getCelular()
    {
        return $this->celular;
    }
    public function getUbicacao()
    {
        return $this->ubicacao;
    }
    public function getInstagram()
    {
        return $this->instagram;
    }
    public function getFacebook()
    {
        return $this->facebook;
    }
    public function getAprovado()
    {
        return $this->aprovado;
    }

    // SETTERS
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }
    public function setTelefone($telefone)
    {
        $this->telefone = $telefone;
    }
    public function setInstagram($instagram)
    {
        $this->instagram = $instagram;
    }
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }
    public function setAprovado($aprovado)
    {
        $this->aprovado = $aprovado;
    }
}
