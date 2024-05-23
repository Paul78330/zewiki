<h2 class="form-title">Mon compte</h2>
<?php

  require_once "../app/models/entities/User.php";
  $currentUser = $_SESSION['user'];

?>
  <div class="container-fluid mt--7">
    <div class="card bg-primary shadow">
      <div class="card-body">
        <form>
          <div class="pl-lg-4">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group focused">
                  <label class="form-control-label" for="username">Nom complet :</label>
                  <input type="text" id="username" class="form-control form-control-alternative" value="<?= $currentUser->getFirstname() ?> <?= $currentUser->getLastname() ?>">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="email">Adresse Email :</label>
                  <input type="email" id="email" class="form-control form-control-alternative" value="<?= $currentUser->getEmail() ?>">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group focused">
                  <label class="form-control-label" for="firstname">Prénom :</label>
                  <input type="text" id="firstname" class="form-control form-control-alternative" value="<?= $currentUser->getFirstname() ?>">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group focused">
                  <label class="form-control-label" for="lastname">Nom :</label>
                  <input type="text" id="lastname" class="form-control form-control-alternative" value="<?= $currentUser->getLastname() ?>">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group focused">
                  <label class="form-control-label" for="creationdate">Date création :</label>
                  <input type="text" id="creationdate" class="form-control form-control-alternative" value=" <?= substr($currentUser->getCreationDate(),0,10) ?>">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group focused">
                  <label class="form-control-label" for="lastconnexion">Dernière connexion :</label>
                  <input type="text" id="lastconnexion" class="form-control form-control-alternative" value="<?= $currentUser->getLastConnectionDate() ?>">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group focused">
                  <label class="form-control-label" for="isadmin">Compte administrateur :</label>
                  <input type="text" id="isadmin" class="form-control form-control-alternative" value="<?php echo ($currentUser->getIsAdmin() == 1) ? 'OUI' : 'NON'; ?>">
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>