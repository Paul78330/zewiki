<?php
  require_once "../app/controllers/UserController.php";
  require_once "../app/models/entities/Folder.php";
  require_once "../app/models/entities/Document.php";
  require_once "../app/models/entities/User.php";

  # Get Selected folder info from $_SESSION:
  $folderSource = $_SESSION['lastSelectedFolderSource'];
  $folderId = $_SESSION['lastSelectedFolderId'];

  # get selected folder object in array

  foreach ($_SESSION[$folderSource] as $folder)
  {
    if($folder->getId()==$folderId)
    {
      # folder found, get infos
      $folderName = $folder->getName(); 
      break;
    }
  }


  ?>
  <h2 class="form-title">Ajouter un nouveau dossier</h2>  

  <form method="POST" action="add-folder" name="myform" id="myform">

    <div class="form-group">
      <label>Dossier sélectionné :</label>
      <label><?= $folderName ?></label>
    </div>
      
    <div class="form-group">
      <label>user id :</label>
      <input type="text" id="userId" name="userId" value="<?= $_SESSION['user']->getId() ?>" readonly>
    </div>

    <div class="form-group">
      <label>id :</label>
      <input type="text" id="parentId" name="parentId" value="<?= $folderId ?>" readonly>
    </div>

    <div class="form-group">
      <label>Source:</label>
      <input type="text" id="source" name="source" value="<?= $folderSource ?>" readonly>
    </div>

    <input name="newFolderName" id="newFolderName" >  
    <input type="submit" name="ajouter" value="Ajouter" title="Ajouter un  nouveau dossier">
  </form>
