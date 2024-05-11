<?php
require_once "../app/controllers/UserController.php";

# home case :
if( isset($_SESSION['nav_choice']) && $_SESSION['nav_choice'] =='home')
{
    # show button for folder context
    if( isset($_SESSION['selectedItem']) && $_SESSION['selectedItem'] =='folder'){
        echo "<a href='show-newfolder-form' data-toggle='tooltip' title='nouveau dossier' class='me-3'><img src ='" . DOSSIER_ICONES . "add_folder.png' width=22></a>";
        echo "<a href='show-editFolder-form' data-toggle='tooltip' title='éditer dossier' class='me-3'><img src='" . DOSSIER_ICONES . "edit_folder.png' width=22></a>";
        echo "<a href='delete-folder' data-toggle='tooltip' title='supprimer dossier' class='me-3'><img src='" . DOSSIER_ICONES . "rm_folder.png' width=22></a>";
        echo "<a href='show-newDocument-form' data-toggle='tooltip' title='nouveau document' class='me-3'><img src ='" . DOSSIER_ICONES . "add_file.png' width=20></a>";
    }
    # show button for document context
    elseif( isset($_SESSION['selectedItem']) && $_SESSION['selectedItem'] =='document'){
        echo "<a href='show-editDocument-form' data-toggle='tooltip' title='éditer document' class='me-3'><img src='" . DOSSIER_ICONES . "edit_file.png' width=20></a>";
        echo "<a href='delete-document' data-toggle='tooltip' title='supprimer document'class='me-3'><img src='" . DOSSIER_ICONES . "rm_file.png' width=20></a>";
    }
}

# Admin case 
    if( isset($_SESSION['nav_choice']) && $_SESSION['nav_choice'] =='admin')
    {
        # show button for folder context
        if( isset($_SESSION['selectedItem']) && $_SESSION['selectedItem'] =='folder'){
            echo "<a href='show-newfolder-form' data-toggle='tooltip' title='nouveau dossier' class='me-3'><img src ='" . DOSSIER_ICONES . "add_folder.png' width=22></a>";
            echo "<a href='show-editFolder-form' data-toggle='tooltip' title='éditer dossier' class='me-3'><img src='" . DOSSIER_ICONES . "edit_folder.png' width=22></a>";
            echo "<a href='delete-folder' data-toggle='tooltip' title='supprimer dossier' class='me-3'><img src='" . DOSSIER_ICONES . "rm_folder.png' width=22></a>";
            echo "<a href='show-newDocument-form' data-toggle='tooltip' title='nouveau document' class='me-3'><img src ='" . DOSSIER_ICONES . "add_file.png' width=20></a>";
        }
        # show button for document context
        elseif( isset($_SESSION['selectedItem']) && $_SESSION['selectedItem'] =='document'){
            echo "<a href='show-editDocument-form' data-toggle='tooltip' title='éditer document' class='me-3'><img src='" . DOSSIER_ICONES . "edit_file.png' width=20></a>";
            echo "<a href='delete-document' data-toggle='tooltip' title='supprimer document'class='me-3'><img src='" . DOSSIER_ICONES . "rm_file.png' width=20></a>";
        }
    
        # Connected user where isAdmin == true : show admins icons (first separator)
        if (isset($_SESSION['user']) && UserController::isSessionAdmin())
        {
           echo "<a><img src='" . DOSSIER_ICONES . "separator.png' width=20></a>";
           echo "<a href='admin-show-users' data-toggle='tooltip' title='lsite des utilisateurs' class='me-3'><img src='" . DOSSIER_ICONES . "users.png' width=20></a>";
    
        }
    
    }
