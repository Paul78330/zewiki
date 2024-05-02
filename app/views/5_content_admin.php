<?php
  require_once "../app/controllers/UserController.php";

    # Access denied for not admin user
     if (!isset($_SESSION['user']) || UserController::isSessionAdmin() == false)
  {
    header("Location: erreur");
    exit;
  }
?>

<main class="border p-2 h-100">
  Vous êtes connecté en mode Administrateur, vous avez les pleins pouvoir...
</main>