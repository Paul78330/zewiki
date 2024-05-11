<?php

require_once "../app/controllers/InputController.php";
require_once "../app/controllers/DisplayController.php";
require_once "../app/controllers/AbstractController.php";
require_once "../app/models/DatabaseManager.php";
require_once "../app/models/entities/User.php";

class AdminController extends AbstractController{

    // Fonctions

    public function accueil(){
        # ajout des variables au tableau à envoyer
        $data = array('title' => 'administration','trace'=>'accueil');

        # ajout des vues au tableau à envoyer
        $viewsListe = array('3_folders_liste.php','4_actions_liste.php','5_content_admin.php');
        $this->render($viewsListe,$data);

    }

    public function redirigerVers($page){
        header("Location: $page");
        exit;
    }

}
