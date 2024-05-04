<?php
  require_once "../app/controllers/UserController.php";
  require_once "../app/models/entities/Folder.php";
  require_once "../app/models/entities/Document.php";
  require_once "../app/models/entities/User.php";

  # Get Selected folder info from $_SESSION:

  $folderId = $_SESSION['lastSelectedFolderId'];

  # get selected folder object in array

  foreach ($_SESSION['tree'] as $folder)
  {
    if($folder->getId()==$folderId)
    {
      # folder found, get infos
      $folderName = $folder->getName(); 
      break;
    }
  }
  ?>
  <h2 class="form-title">Renommer : <?= $folderName ?></h2>  

  <form method="POST" action="rename-folder" name = "add-folder" id = "add-folder">
    <div class="form-group">
        <input type="text" id="folderId" name="folderId" value="<?= $folderId ?>" hidden>
        <label>Nouveau nom :</label>
        <input name="newFolderName" id="newFolderName" >  
        <input type="submit" name="ajouter" value="Renommer" title="Renommer le dossier">
    </div>
  </form>