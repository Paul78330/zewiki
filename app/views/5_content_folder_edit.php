  <h2 class="form-title">Edition</h2>  

  <form method="POST" action="update-folder" name="updatefolder" id="updatefolder">

    <div class="form-group">
      <input type="text" id="folderId" name="folderId" value="<?= $datas['lastSelectedFolderId'] ?>" hidden>
      <input type="text" id="folderName" name="folderName" value="">
    </div>

    <input type="submit" name="enregistrer" value="enregistrer" title="Enregistrer le dossier">

    
  </form>
