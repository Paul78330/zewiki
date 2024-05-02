<?php
    require_once "../app/models/entities/User.php";
    // methodes communes a tous les controleurs, class abstraite ne peut etre instancier mais seulement héritée
    abstract class AbstractController {
    // Méthode pour inclure les données le template
    public function render($datas=[]){
       
        ob_start();
        extract($datas);
       
        # Toutes les variables sont intégrées dans le template qui est affiché
        include VIEWS_PATH. 'template.php';
    }

    public function redirectTo(string $page):void {
        header("Location: " . $page);
        exit();
    }

}