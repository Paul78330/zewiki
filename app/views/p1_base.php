<?php

        # Verify if user is connected
        if ($connected)
        {
            # Connected user : show button 'paramètres', 'déconnexion' and 'Mon compte'
            echo '<li class="nav-item">';
                echo "<a class='nav-link mx-2' href='p1-ex-myAccount' data-toggle='tooltip' title='Mon compte' ><img src ='" . DOSSIER_ICONES . "p1_my_account.png' width=25></a>";  
            echo '</li>';

            echo '<li class="nav-item">';
                echo "<a class='nav-link mx-2' href='p1-ex-disconnect' data-toggle='tooltip' title='Déconnexion' ><img src ='" . DOSSIER_ICONES . "p1_disconnect.png' width=25></a>"; 
            echo '</li>';

        }        
        else
        {
            # User not connected : show button 'connexion' and 'créer un compte'
            echo '<li class="nav-item">';
                echo "<a class='nav-link mx-2' href='p1-sf-addAccount' data-toggle='tooltip' title='Créer un compte' ><img src ='" . DOSSIER_ICONES . "p1_add_account.png' width=25></a>";  
            echo '</li>';

            echo '<li class="nav-item">';
                echo "<a class='nav-link mx-2' href='p1-sf-connection' data-toggle='tooltip' title='Connexion' ><img src ='" . DOSSIER_ICONES . "p1_connexion.png' width=25></a>"; 
            echo '</li>';
        }
