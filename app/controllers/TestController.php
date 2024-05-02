<?php
require_once "../app/models/DatabaseManager.php";
require_once "../app/controllers/AbstractController.php";




class TestController extends AbstractController{

    # Fonction 
    
    public function show(){

        var_dump($_SESSION);
        die();

        $toast = DisplayController::createAlerte('ca marche');
        # Set datas in $datas array
        $datas['p2_view'] = 'p2_base.php';
        $datas['p3_view'] = 'p3_base.php';
        $datas['p5_view'] = 'p5_messageRedirect.php';
        $datas['toast']  = $toast;


        # add vars to template and call template
        $this->render($datas);


    }



}