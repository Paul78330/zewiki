<?php
    require_once "../app/controllers/UserController.php";

    # Connected user where isAdmin == true : show icons
    if (isset($_SESSION['user']) && UserController::isSessionAdmin())
    {
        echo "<a href='show-newfolder-form' data-toggle='tooltip' title='nouveau dossier'><img src ='" . DOSSIER_ICONES . "folder-plus.svg'></a>";
        echo "<a href='show-newDocument-form' data-toggle='tooltip' title='nouveau document'><img src='" . DOSSIER_ICONES . "file-plus.svg'></a>";
    }
?>