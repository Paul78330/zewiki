<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zewiki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= DOSSIER_CSS ?>style.css">
</head>
<body class="vh-100">

    <?php
        require_once "../app/controllers/UserController.php";
        # Vars depending user state
        $connected = isset($_SESSION['user']);
        $isAdmin = UserController::isSessionAdmin();
    ?>
    <!-- début div bootstrap-->
    <div class="container-fluid border border-primary p-2 h-100">

        <!--Barre Entête de l'application row 1 P1 -->
        <header class="navbar navbar-expand-md p-1">
            <div class="container-fluid">
                <?php
                    include('../app/views/p1_base.php');
                ?>
            </div>
        </header>

        <div class="row border p-2 h-100">
            <div class="col-2 p-2 h-100">
                <!-- 2-nav_icons div P2 -->
                <div id="choix_site" class="border p-2 d-flex justify-content-around">
                    <?php
                        include('../app/views/p2_base.php');
                    ?>
                </div>

                <!--items tree P3-->
                <div id="folders_list" class="border p-2 h-100">
                    <?php
                        include('../app/views/' . $datas['p3_view']);
                    ?>
                </div>
            </div>

            <div class="col-10 p-2">
                <?php
                    # Show actions button bar if needed
                    if (isset ($datas['p4_view']))
                    {
                        #Actions buttons bar P4
                        echo('<div id="actions" class="border p-2">');
                        #Button list
                        include('../app/views/' . $datas['p4_view']);
                        echo '</div>';
                    }
                ?>

                <!--Main content P5 -->
                <main class="border p-2 h-100">
                    <?php
                        include('../app/views/' . $datas['p5_view']);
                    ?>
                </main>
            </div>
        </div>
    <!-- fin div bootstrap-->
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= DOSSIER_JS ?>script.js"></script>
    <script src="<?= DOSSIER_JS ?>tinymce/tinymce.min.js"></script>
    <script src="<?= DOSSIER_JS ?>tinymce/init-tinymce.js"></script>
</html>











