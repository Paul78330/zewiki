<h2> Informations </h2>
<?php
    if(isset($_SESSION['alert']['message']))
    {
        echo $_SESSION['alert']['message'];
        unset($_SESSION['alert']['message']);
    }


?>
