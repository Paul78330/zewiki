<?php
  require_once "../app/controllers/UserController.php";

    # Access denied for not admin user
     if (!isset($_SESSION['user']) || UserController::isSessionAdmin() == false)
  {
    header("Location: erreur");
    exit;
  }
?>

<main class="border p-2 h-100">
  <!-- tabs list -->
  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">Utilisateurs</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab" aria-controls="logs" aria-selected="false">Logs</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tokens-tab" data-bs-toggle="tab" data-bs-target="#tokens" type="button" role="tab" aria-controls="tokens" aria-selected="false">Tokens</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="outils-tab" data-bs-toggle="tab" data-bs-target="#outils" type="button" role="tab" aria-controls="outils" aria-selected="false">Outils</button>
    </li>
  </ul>

<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
    <!-- -------------------------------------------------Users  tab  --------------------------------------------------->
    <br>
    <h3> Utilisateurs</h3>
      <table class="table">
        <thead class="thead-dark">
          <tr>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Admin</th>
            <th>Activé</th>
            <th>Action</th>
            <th>Affecter le rôle</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $users = $datas['users'];
            # loop on $user to show informations for each user
            foreach ($users as $user)
            {
              echo "<tr>";
                echo "<td>" . $user['user_firstname'] . "</td>";
                echo "<td>" . $user['user_lastname'] . "</td>";
                echo "<td>" . $user['user_email'] . "</td>";
                echo "<td>" . ($user['user_isadmin'] == 1 ? 'oui' : 'non') . "</td>";
                echo "<td>" . ($user['is_active'] == 1 ? 'oui' : 'non') . "</td>";
                echo "<td>".($user['is_active'] == 1 ? "<a href='user-disable?id=" . $user['user_id'] . "' class='btn btn-danger'>Désactiver</a>":"<a href='user-enable?id=" . $user['user_id'] . " ' class='btn btn-success'>Activer</a>"). "</td>";
                echo "<td>".($user['user_isadmin'] == 1 ? "<a href='user-downgrade?id=" . $user['user_id'] . "' class='btn btn-danger'>User</a>":"<a href='user-upgrade?id=" . $user['user_id'] . " ' class='btn btn-success'>Admin</a>"). "</td>";
                echo "<td><a href='user-delete?id=" . $user['user_id'] . "' class='btn btn-danger'>Supprimer</a></td>";
              echo "</tr>";
            }
          ?>
        </tbody>
    </table>
  </div>

  <div class="tab-pane fade" id="logs" role="tabpanel" aria-labelledby="logs-tab">
    <!-- -------------------------------------------------Logs  tab  --------------------------------------------------->
    <br>
    <h3> Logs</h3>
    
    <table class="table">
      <thead class="thead-dark">
        <tr>
          <th>Date</th>
          <th>Catégorie</th>
          <th>Criticité</th>
          <th>IP Source</th>
          <th>User</th>
          <th>Message</th>
        </tr>
      </thead>
      <tbody>
        <?php
              $logs = $datas['logs'];
              # loop on $log to show informations for each log
              foreach ($logs as $log) 
              {
              echo "<tr>";
                echo "<td>" . $log['log_creationdate'] . "</td>";
                echo "<td>" . $log['log_category'] . "</td>";
                echo "<td>" . $log['log_level'] . "</td>";
                echo "<td>" . ($log['log_ip']) . "</td>";
                echo "<td>" . ($log['log_user']) . "</td>";
                echo "<td>".($log['log_message']). "</td>";
              echo "</tr>";
              }
        ?>
      </tbody>
    </table>
  </div>

  <div class="tab-pane fade" id="tokens" role="tabpanel" aria-labelledby="tokens-tab">
  <!-- -------------------------------------------------Logs  tab  --------------------------------------------------->
    <br>
    <h3>Tokens</h3>
    
    <table class="table">
      <thead class="thead-dark">
        <tr>
          <th>Date</th>
          <th>Source</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        <?php
              $tokens = $datas['tokens'];
              # loop on $user to show informations for each user
              foreach ($tokens as $token) 
              {
                  echo "<tr>";
                  echo "<td>" . $token['created_at'] . "</td>";
                  echo "<td>" . $token['token_source'] . "</td>";
                  echo "<td>" . $token['token_email'] . "</td>";
                echo "</tr>";
              }
        ?>
      </tbody>
    </table>
  </div>

  <div class="tab-pane fade" id="outils" role="tabpanel" aria-labelledby="outils-tab">
  <!-- -------------------------------------------------Logs  tab  --------------------------------------------------->
    <br>
    <h3>Outils</h3>
    

  </div>
</div>
</main>