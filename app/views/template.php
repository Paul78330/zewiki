<?php
            require_once "../app/controllers/UserController.php";
            # Vars depending user state
            $connected = isset($_SESSION['user']);
            $isAdmin = UserController::isSessionAdmin();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/x-icon" href="<?= DOSSIER_ICONES ?>favicon.ico">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
        <link rel="stylesheet" href="<?= DOSSIER_CSS ?>style.css">
        <title>Zewiki</title>
      </head>
    <body class="d-flex flex-column min-vh-100">
        <header>
            <nav  class="navbar navbar-expand-sm p-1 fixed-top">
                <div class="container-fluid">
                    <a class="navbar-brand" href="https://www.zewiki.fr"><img src ="<?= DOSSIER_ICONES ?>zewiki.svg" width=75></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto ">
                            <!-- List items inclusions depending user-->
                            <?php
                                include('../app/views/p1_base.php');
                            ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <!-- left panel with 2 subdiv (choix_site & folders_list) -->
        <main class="d-flex flex-wrap flex-md-nowrap">
            <div id="leftpanel" class="d-flex flex-column flex-grow-1">
                <!-- 2-nav_icons div P2 -->
                    <div id="choix_site" class="border p-2 d-block">
                        <?php
                            include('../app/views/p2_base.php');
                        ?>
                    </div>

                <!--items tree P3-->
                <div id="folders_list" class="flex-grow-1 p-2">
                    <?php
                        include('../app/views/' . $datas['p3_view']);
                    ?>
                </div>
            </div>
        <!-- separator panel used to rezize left and right panels -->
            <div id="separator" class="border-1 min-vh-100">
            </div>

        <!-- right panel with 2 subdiv (actions bar and content) -->
            <div id="rightpanel" class="d-flex flex-column flex-grow-1">
                <!-- actions bar div -->    
                <?php
                    #  Show actions button bar if needed -->
                    if (isset ($datas['p4_view']))
                    {
                        #Actions buttons bar P4
                        echo('<div id="actions" class="border p-2 d-block">');
                        #Button list
                        include('../app/views/' . $datas['p4_view']);
                        echo '</div>';
                    }
                ?>

                <!-- content div -->
                    <div class="d-block p-3">
                        <?php
                            include('../app/views/' . $datas['p5_view']);
                        ?>
                    </div>
 
            </div>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="<?= DOSSIER_JS ?>script.js"></script>
        <script src="<?= DOSSIER_JS ?>tinymce/tinymce.min.js"></script>
        <script src="<?= DOSSIER_JS ?>tinymce/init-tinymce.js"></script>
    </body>
</html>