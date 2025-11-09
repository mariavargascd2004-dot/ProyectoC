<?php
require_once '../config/database.php';
require_once '../models/modelsDAO/ImagemGaleriaDAO.php';

class imagemGaleriaHelper
{
    private $imagemGaleriaDAO;

    public function __construct()
    {
        try {
            $db = (new Database())->getConnection();
            $this->imagemGaleriaDAO = new ImagemGaleriaDAO($db);
        } catch (Exception $e) {
            error_log("Error fatal en addminAssociadoHelper al conectar a BD: " . $e->getMessage());
        }
    }

    public function obterImagemsComIdEmprendimento($id)
    {
        try {
            return $this->imagemGaleriaDAO->listarPorEmprendimento($id);
        } catch (Exception $e) {
            error_log("Error buscando emprendimiento $id: " . $e->getMessage());
            return null;
        }
    }
}
