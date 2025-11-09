<?php
require_once '../config/database.php';
require_once '../models/modelsDAO/ImagemFabricacaoDAO.php';

class imagemFabricacaoHelper
{
    private $imagemFabricacaoDAO;

    public function __construct()
    {
        try {
            $db = (new Database())->getConnection();
            $this->imagemFabricacaoDAO = new ImagemFabricacaoDAO($db);
        } catch (Exception $e) {
            error_log("Error fatal en addminAssociadoHelper al conectar a BD: " . $e->getMessage());
        }
    }

    public function obterImagemsComIdEmprendimento($id)
    {
        try {
            return $this->imagemFabricacaoDAO->listarPorEmprendimento($id);
        } catch (Exception $e) {
            error_log("Error buscando emprendimiento $id: " . $e->getMessage());
            return null;
        }
    }
}
