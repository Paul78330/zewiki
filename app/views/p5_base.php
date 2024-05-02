<?php
    if(!isset($_SESSION['user'])){
        # Show welcome message for visitor
        echo '<p>Vous n\'êtes pas connecté à un compte, vous ne pouvez donc que visualiser le contenu public. Si vous souhaitez créer vos propres documents pour vous même ou à partager vous devez vous connecter.</p>
        <p>Vous pouvez vous connecter ou créer un compte si vous n\'en avez pas en cliquant sur les boutons en haut à droite.</p>';
    }
    else{
        # Show welcome message for connected user
        echo '<p>Bienvenue dans l\'application Zewiki';
      }
      
?>