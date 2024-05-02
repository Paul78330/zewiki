  <h2 class="form-title">Edition</h2>  

  <form method="POST" action="update-document" name="myform" id="myform">

    <div class="form-group">
      <input type="text" id="documentId" name="documentId" value="<?= $_SESSION['lastDocument']['id'] ?>" hidden>
    </div>

    <input type="submit" name="enregistrer" value="enregistrer" title="Enregistrer le document">

    <textarea name="documentContent" id="documentContent" cols="130" rows="30">
      <?= $_SESSION['lastDocument']['content'] ?>
    </textarea>
    
  </form>
