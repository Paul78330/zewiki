<h2>Inscription</h2>

<form action="p5-register" method="POST" id="p5-register">
  

    <!-- Firstname input -->
        <div class="form-outline mb-4">
        <label class="form-label" for="firstname">Prénom</label>
        <input type="text" id="firstname" name="firstname" class="form-control" />
    </div>

    <!-- Lastname input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="lastname">Prénom</label>
        <input type="text" id="lastname" name="lastname" class="form-control" />
    </div>

    <!-- Email input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="email">Addresse e-mail</label>
        <input type="email" id="email" name="email" class="form-control" />
    </div>

    <!-- Password input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="password">Mot de passe</label>
        <input type="password" id="password" name="password" class="form-control" />
    </div>

    <!-- Password validation input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="password2">Confirmez votre mot de passe</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" />
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