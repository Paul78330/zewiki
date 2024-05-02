<h2 class="form-title">Mon compte</h2>
<?php

  require_once "../app/models/entities/User.php";
  $currentUser = $_SESSION['user'];


?>
<div class="form-group">
    <label>prénom :</label>
    <label><?= $currentUser->getFirstname() ?></label>
</div>

<div class="form-group">
  <label>nom :</label>
  <label><?= $currentUser->getLastname() ?></label>
</div>

<div class="form-group">
  <label>email :</label>
  <label><?= $currentUser->getEmail() ?></label>
</div>

<div class="form-group">
  <label>compte admin :</label>
  <label>
  <?php 
    if($currentUser->getIsAdmin() == 1){
      echo 'oui';
    } 
    else{
      echo 'non';
    }
    ?>
  </label>
</div>

<div class="form-group">
  <label>Date de création du compte :</label>
  <label>
  <?= $currentUser->getCreationDate() ?>
  </label>
</div>

<div class="form-group">
  <label>Dernière connexion :</label>
  <label>
  <?= $currentUser->getLastConnectionDate() ?>
  </label>
</div>