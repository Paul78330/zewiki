<h2>Recréer un nouveau mot de passe</h2>

<form action="newPassword" method="POST" id="new_password">
  
    <!-- Password input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="password">Mot de passe</label>
        <input type="password" id="password" name="password" class="form-control" />
    </div>

    <!-- Password validation input -->
    <div class="form-outline mb-4">
        <label class="form-label" for="password2">Confirmez votre mot de passe</label>
        <input type="password" id="password2" name="password2" class="form-control" />
    </div>

  <!-- Submit button -->
  <p><button type="submit" class="btn btn-primary btn-block mb-4">Enregistrer</button></p>

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
      echo 'Message : ' . $_SESSION['alert']['message'];
      # Reset error message
      unset($_SESSION['alert']);
    }
  ?>
</form>