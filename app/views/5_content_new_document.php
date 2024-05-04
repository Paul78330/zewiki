<?php
  require_once "../app/controllers/UserController.php";
  require_once "../app/models/entities/Folder.php";
  require_once "../app/models/entities/Document.php";
  require_once "../app/models/entities/User.php";

  # get Selected folder info from $_SESSION:
  $folderSource = $_SESSION['lastSelectedFolderSource'];
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
  <h2 class="form-title">Ajouter un nouveau document à <?= $folderName ?> </h2>  

  <form method="POST" action="add-document" name="add-document" id="add-document">
    
    <div class="form-group">
      <input type="text" id="parentId" name="parentId" value="<?= $folderId ?>" hidden>
      <input type="text" id="userId" name="userId" value="<?= $_SESSION['user']->getId() ?>" hidden>
      <input type="text" id="source" name="source" value="<?= $folderSource ?>" hidden>
      <label for="newDocumentName">Nom :</label>
      <input name="newDocumentName" id="newDocumentName" > 
      <input type="submit" name="ajouter" value="Ajouter" title="Ajouter un nouveau document">
    </div>
  </form>
