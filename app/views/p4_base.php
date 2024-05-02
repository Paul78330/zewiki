<?php
require_once "../app/controllers/UserController.php";

# home case :
if( isset($_SESSION['lastSelectedFolderSource']) && $_SESSION['lastSelectedFolderSource'] =='home'){
    # No selected item
}


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

echo ("pas de bouton pour toi t'es trop moche");