<form action="p5-ex-connection" method="POST" id="connexion">
  
  <h2>Connexion</h2>

  <!-- Email input -->
  <div class="form-outline mb-4">
    <label class="form-label" for="email">Adresse e-mail</label>
    <input type="email" id="email" name="email" class="form-control" required />
  </div>

  <!-- Password input -->
  <div class="form-outline mb-4">
    <label class="form-label" for="password">Mot de passe</label>
    <input type="password" id="password" name="password" class="form-control" required />
  </div>

  <!-- Submit button -->
  <button type="submit" class="btn btn-primary btn-block mb-4">Connexion</button>
  <p><a class="btn btn-secondary" href="p5-sf-resetPassword">Mot de passe oublié ?</a></p>

  <!-- Error message-->
  <?php
    if (isset($_SESSION['alert']['message']))
    {
      # Show error message if exists
      echo 'Message : ' . $_SESSION['alert']['message'];
      # Reset error message
      unset($_SESSION['alert']);
    }
  ?>
</form>