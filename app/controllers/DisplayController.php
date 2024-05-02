<?php

// a voir ulterieurement si utile

class DisplayController
{

    const ROUGE ='danger';
    const ORANGE ='warning';
    const VERT = 'success';
    const BLEU = 'info';

    public static function messageAlert(string $message,string $type):void
    {
        $_SESSION['alert']=['type'=>$type,'message'=>$message];
    }


    public static function createAlerte($message)
    {
        $alerte = "
            <div class='alert alert-success' role='alert'>" . 
                $message 
            ."</div>
        ";
        return $alerte;
    }

    public static function creerToast($titre,$message)
    {
        $toast = "
        <div class='toast show bg-success text-white fs-6' role='alert' aria-live='assertive' aria-atomic='true'>
        <div class='toast-header'>
        <strong class='me-auto'>" . $titre ."</strong>
        <button type='button' class='btn-close' data-bs-dismiss='toast' aria-label='Close'></button>
        </div>
        <div class='toast-body'>" . $message . "</div>
        </div>";
        return $toast;
    }
    public static function creerRedirectToast($titre,$message,$url)
    {
        $toast = "
        <div class='toast show bg-success text-white fs-6' role='alert' aria-live='assertive' aria-atomic='true' data-redirect='" . $url. "'>
        <div class='toast-header'>
        <strong class='me-auto'>" . $titre ."</strong>
        <button type='button' class='btn-close' data-bs-dismiss='toast' aria-label='Close'></button>
        </div>
        <div class='toast-body'>" . $message . "</div>
        </div>
        <p> Vous allez être redirigé vers www.google.fr dans <span id='compteur'>10</span> secondes </p> 
        ";
        return $toast;
    }

    public static function creerRedirectToast2($message,$url)
    {
        $toast = "
        <h2>Information</h2>
        <div class='toast align-items-center text-bg-success border-0 show' role='alert' aria-live='assertive' aria-atomic='true' data-redirect='" . $url. "'>
        <div class='d-flex'>
          <div class='toast-body'> " . 
          $message 
          . "
          </div>
          <button type='button' class='btn-close btn-close-white me-2 m-auto' data-bs-dismiss='toast' aria-label='Close'></button>
        </div>
      </div>
      <br>
      <p> Vous allez être redirigé vers www.google.fr dans <span id='compteur'>10</span> secondes </p> 
        ";
        return $toast;
    }

}

