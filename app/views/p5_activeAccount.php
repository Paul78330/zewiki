<h2>Activation de votre compte</h2>

<form action="p5-ex-activeAccount" method="POST" id="p5-ex-activeAccount">
  
<div class="form-outline mb-4 b.bg-gainsboro">
    <label class="form-label" for="email">Adresse e-mail</label>
    <input type="email" id="email" name="email" class="form-control" required />
  </div>


  <!-- Submit button -->
  <p><button type="submit" class="btn btn-primary btn-block mb-4">Envoyer un code</button></p>

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