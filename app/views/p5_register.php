<h2>Inscription</h2>

<form action="p5-register" method="POST" id="p5-register">

<!-- Firstname input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="firstname">Prénom</label>
        <input type="text" id="firstname" name="firstname" class="form-control" required pattern="^[A-Za-zÀ-ÖØ-öø-ÿ\-]{1,50}$" title="50 caractères maximum, accents autorisés"/>
    </div>

    <!-- Lastname input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="lastname">Prénom</label>
        <input type="text" id="lastname" name="lastname" class="form-control"required pattern="^[A-Za-zÀ-ÖØ-öø-ÿ\-]{1,50}$" title="50 caractères maximum, accents autorisés" />
    </div>

    <!-- Email input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="email">Addresse e-mail</label>
        <input type="email" id="email" name="email" class="form-control"  title="Veuillez renseigner une adresses Email valide" required />
    </div>

    <!-- Password input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="password">Mot de passe</label>
        <input type="password" id="password" name="password" class="form-control" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,25}$" title="Le mot de passe doit contenir entre 8 et 25 caractères, au moins une majuscule, une minuscule, un chiffre et un caractère spécial." required />
    </div>

    <!-- Password validation input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="confirm_password">Confirmez votre mot de passe</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,25}$" title="Le mot de passe doit contenir entre 8 et 25 caractères, au moins une majuscule, une minuscule, un chiffre et un caractère spécial." required />
    </div>

  <!-- Submit button -->
  <p><button type="submit" class="btn btn-primary btn-block mb-4">Inscription</button></p>

    <!-- toast -->
    <?php 
        if(isset($datas['toast']))
        {
          echo $datas['toast'];
        }; 
      ?>

  <!-- Error message-->
  <?php
    if (isset($_SESSION['alert']['message']))
    {
      # Show error message if exists
      $_SESSION['alert']['message'];
      # Reset error message
      unset($_SESSION['alert']);
    }
  ?>
</form>