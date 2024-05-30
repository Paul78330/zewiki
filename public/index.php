<?php

require_once '../app/controllers/UserController.php';
require_once '../app/controllers/TestController.php';
require_once '../app/controllers/AdminController.php';
require_once '../app/models/entities/User.php';
session_start();

# Créatiion des constantes simplifiant l'accès aux fichiers
define('DOSSIER_CSS', 'public/assets/css/');
define('DOSSIER_JS', 'public/assets/js/');
define('DOSSIER_ICONES', 'public/assets/icones/');
define('VIEWS_PATH','../app/views/');

# Appels des différents controleurs nécessaires au routeur

$usercontroller = new UserController();
$admin = new AdminController();


try{
    # Routing to controller depending url 
    if (empty($_GET['page'])){
        # $_GET empty : show default welcome page
        $usercontroller->welcome();
        exit();
    }
    else{
        # Get required url with page (rewrite url with htaccess in public folder)
        $url = $_GET['page']; 
        switch($url){

            # Login button (sf = show form)
            case 'p1-sf-connection' :
                $usercontroller->p1_sf_connection();
                break;

            # Disconnect (ex = ex = execute)
            case 'p1-ex-disconnect' :
                $usercontroller->p1_ex_disconnect();
                break;
            
            # Create account button (sf = show form)
            case 'p1-sf-addAccount' :
                $usercontroller->p1_sf_addAccount();
                break;

            # Show my account informations
            case 'p1-ex-myAccount' :
                $usercontroller->p1_ex_myAccount();
                break;

            # Connexion button (ex = execute)
            case 'p5-ex-connection' :
                $usercontroller->p5_ex_connection();
                break;
            
            # Reset password button (sf = show form)
            case 'p5-sf-resetPassword' :
                $usercontroller->p5_sf_resetPassword();
            break;

            #  Reset password button (ex = execute)
            case 'p5-ex-resetPassword' :
                $usercontroller->p5_ex_resetPassword();
            break;

            # Show form to active account  (sf = show form)
            case 'p5-sf-activeAccount' :
                $usercontroller->p5_sf_activeAccount();
            break;

            # Active account  (ex = execute)
            case 'p5-ex-activeAccount' :
                $usercontroller->p5_ex_activeAccount();
            break;

            # Reset password from link show form
            case 'resetPassword' :
                $usercontroller->resetPasswordSF();
            break;

            # Set new password 
            case 'newPassword' :
                $usercontroller->newPassword();
            break;

            # Click on 'Inscription' button from p5-register.php form
            case 'p5-register' :
                $usercontroller->p5_ex_register();
                break;

            # Click on connection button
            case 'connexion' :
                $usercontroller->connexion();
                break;

            # Type the url in browser (no button)
            case 'test' :
                $test->show();
                break;

            # Default welcome page ( a supprimer?)
            case 'welcome' :
                $usercontroller->welcome();
                break;

            # Admin page
            case 'administration' :
                $admin->accueil();
                break;

            # Click on disconnect button
            case 'exit' :
                $usercontroller->exit();
                break;

            # Click on nav-bar buttons
            case 'nav' :
                $usercontroller->nav();
                break;

            # Click on document : view the document in content zone ( based on id document)
            case 'view' :
                $usercontroller->view('5_content_document.php');
                break;

            # Click on add folder button from action list : 
            case 'show-newfolder-form' :
                $usercontroller->showNewFolderForm();
                break;

            # Click on edit folder button from action list : 
            case 'show-editFolder-form' :
                $usercontroller->showEditFolderForm();
                break;

                # Click on add document button from action list : 
            case 'show-newDocument-form' :
                $usercontroller->showNewDocumentForm();
                break;

            # Click on edit document button from action list : 
            case 'show-editDocument-form' :
                $usercontroller->view('5_content_document_edit.php');
                break;

            # Click on submit button from content_new_folder : 
            case 'add-folder' :
                $usercontroller->addFolder();
                break;
            
            # Click on submit button from content_new_folder : 
            case 'rename-folder' :
                $usercontroller->renameFolder();
                break;
   
                # Click on submit button from content_new_folder : 
            case 'add-document' :
                $usercontroller->addDocument();
                break;

            # Click on save button from edit document form : 
            case 'update-document' :
                $usercontroller->updateDocument();
                break;

            # Click on delete document button from actions list : 
                case 'delete-document' :
                    $usercontroller->deleteDocument();
                    break;

            # Click on a folder reduce/expand it
            case 'folders' :
                $usercontroller->toggleFolder();
                break;


            # Click on delete folder button from actions list : 
            case 'delete-folder' :
                $usercontroller->deleteFolder();
                break;

            # Admin part ---------------

            # enable user from admin panel
            case 'user-enable' :
                $usercontroller->user_enable();
                break;

            # disable user from admin panel
            case 'user-disable' :
                $usercontroller->user_disable();
                break;

            # delete user from admin panel
            case 'user-delete' :
                $usercontroller->user_delete();
                break;

            # upgrade user to admin from admin panel
            case 'user-upgrade' :
                $usercontroller->user_upgrade();
                break;

            # downgrade user from admin to user from admin panel
            case 'user-downgrade' :
                $usercontroller->user_downgrade();
                break;

            # Unknown page : 404    
            default:
                throw new Exception("la page n'existe pas");
        }
    }

}
catch(Exception $exception){
    $error = $exception->getMessage();
    include VIEWS_PATH. 'error.php';
}


