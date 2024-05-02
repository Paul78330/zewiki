<?php
    # View showing icons depending of user rights

    # Connected user : show icons 'home' and 'share'
    if ($connected)
    {
        echo "<a href='nav?choice=home' data-toggle='tooltip' title='Home'><img src ='" . DOSSIER_ICONES . "house.svg'></a>";
        echo "<a href='nav?choice=shares' data-toggle='tooltip' title='Partages'><img src='" . DOSSIER_ICONES . "share.svg'></a>";
    }

    # Everybody :  show icon 'Public'
        echo "<a href='nav?choice=common' data-toggle='tooltip' title='common' id='common'><img src='" .  DOSSIER_ICONES . "people.svg'></a>";

    # Connected and admin user : show admin icon

    if ($connected && $isAdmin)
    {   
        echo "<a href='nav?choice=admin' data-toggle='tooltip' title='administration'><img src ='" . DOSSIER_ICONES . "tools.svg'></a>";
    }

?>