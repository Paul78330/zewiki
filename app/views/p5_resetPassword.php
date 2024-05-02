<form action="p5-ex-resetPassword" method="POST" id="reset">
  
  <h2>Reinitialisation de votre mot de passe</h2>

  <p>Saisissez votre adresse e-mail, un lien de reinitialisation vous sera envoyé.</p>

  <!-- Email input -->
  <div class="form-outline mb-4">
    <label class="form-label" for="email">Adresse e-mail</label>
    <input type="email" id="email" name="email" class="form-control"  />
  </div>

  <!-- Submit button -->
  <button type="submit" class="btn btn-primary btn-block mb-4">Envoyer</button>
  <br>
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

