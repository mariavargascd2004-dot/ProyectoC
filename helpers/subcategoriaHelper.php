<?php
require_once '../config/database.php';
require_once '../models/modelsDAO/SubCateogriaDAO.php';

class subcategoriaHelper
{
    private $subcategoriaDAO;

    public function __construct()
    {
        try {
            $db = (new Database())->getConnection();
            $this->subcategoriaDAO = new SubCateogriaDAO($db);
        } catch (Exception $e) {
            error_log("Error fatal en addminAssociadoHelper al conectar a BD: " . $e->getMessage());
        }
    }

    public function obtenerSubCategoriasDelEmprendimiento($idCategoria)
    {
        try {
            return $this->subcategoriaDAO->trearSubCategorias($idCategoria);
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            return null;
        }
    }
}
