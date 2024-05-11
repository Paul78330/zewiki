<?php
require_once "../app/models/DatabaseManager.php";
require_once "../app/controllers/AbstractController.php";




class TestController extends AbstractController{

    # Fonction 
    
    public function show(){

        $databasemanager = new DatabaseManager;
        $result = $databasemanager->export();
        if($result)
        {
            $message = 'ok';
        }


        # Set datas in $datas array
        $datas['p2_view'] = 'p2_base.php';
        $datas['p3_view'] = 'p3_base.php';
        $datas['p5_view'] = 'test.php';
        $datas['test'] = $message;


        # add vars to template and call template
        $this->render($datas);


    }



}