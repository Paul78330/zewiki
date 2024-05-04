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
  <h2 class="form-title">Ajouter un nouveau dossier a <?= $folderName ?></h2>  

  <form method="POST" action="add-folder" name = "add-folder" id = "add-folder">

    <div class="form-group">
      <input type="text" id="source" name="source" value="<?= $_SESSION['source'] ?>" hidden>
      <input type="text" id="userId" name="userId" value="<?= $_SESSION['user']->getId() ?>" hidden>
      <input type="text" id="parentFolderId" name="parentFolderId" value="<?= $folderId ?>" hidden>
    </div>

    <input name="newFolderName" id="newFolderName" >  
    <input type="submit" name="ajouter" value="Ajouter" title="Ajouter un nouveau dossier">
  </form>
