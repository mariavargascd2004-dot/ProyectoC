<?php

class Emprendimento
{
    private $idEmprendimento;
    private $adminAssociado_idUsuario;
    private $nome;
    private $logo;
    private $pooster;
    private $corPrincipal;
    private $corSecundaria;
    private $historia;
    private $processoFabricacao;
    private $telefone;
    private $celular;
    private $horarios;
    private $ubicacao;
    private $instagram;
    private $facebook;
    private $aprovado;

    public function __construct($adminAssociado_idUsuario, $nome, $corPrincipal, $corSecundaria, $historia, $processoFabricacao, $celular, $horarios, $ubicacao)
    {
        $this->adminAssociado_idUsuario = $adminAssociado_idUsuario;
        $this->nome = $nome;
        $this->corPrincipal = $corPrincipal;
        $this->corSecundaria = $corSecundaria;
        $this->historia = $historia;
        $this->processoFabricacao = $processoFabricacao;
        $this->celular = $celular;
        $this->horarios = $horarios;
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
    public function getCorPrincipal()
    {
        return $this->corPrincipal;
    }
    public function getCorSecundaria()
    {
        return $this->corSecundaria;
    }
    public function getLogo()
    {
        return $this->logo;
    }
    public function getPooster()
    {
        return $this->pooster;
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
    public function getHorarios()
    {
        return $this->horarios;
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
    public function setPooster($pooster)
    {
        $this->pooster = $pooster;
    }
    public function setCorPrincipal($corPrincipal)
    {
        $this->corPrincipal = $corPrincipal;
    }
    public function setCorSecundaria($corSecundaria)
    {
        $this->corSecundaria = $corSecundaria;
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
