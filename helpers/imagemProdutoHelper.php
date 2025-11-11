<?php
require_once '../config/database.php';
require_once '../models/modelsDAO/ImagemProdutoDAO.php';

class imagemProdutoHelper
{
    private $imagemProdutoDAO;

    public function __construct()
    {
        try {
            $db = (new Database())->getConnection();
            $this->imagemProdutoDAO = new ImagemProdutoDAO($db);
        } catch (Exception $e) {
            error_log("Error fatal en addminAssociadoHelper al conectar a BD: " . $e->getMessage());
        }
    }

    public function obterImagemsComIdProduto($id)
    {
        try {
            return $this->imagemProdutoDAO->listarPorProduto($id);
        } catch (Exception $e) {
            error_log("Error buscando emprendimiento $id: " . $e->getMessage());
            return null;
        }
    }
}
