/*
addEventListener("DOMContentLoaded", start);
function start(){
    var toast = document.querySelector('.toast');
        setTimeout(function(){
        toast.classList.remove('show');
        if (toast.dataset.redirect !== 'undefined' )
        {
            window.location.replace(toast.dataset.redirect);
        }
    },10000);

}*/
function start() {
    // Fonction pour décrémenter la valeur d'un élément par son ID
    function decrementValueById(elementId) {
        var element = document.getElementById(elementId);
        if (element) {
            var currentValue = parseInt(element.textContent);
            if (!isNaN(currentValue)) {
                var newValue = currentValue - 1;
                element.textContent = newValue;
            } else {
                console.error("Le contenu de l'élément n'est pas un nombre.");
            }
        } else {
            console.error("L'élément avec l'ID spécifié n'existe pas.");
        }
    }

    // Fonction pour démarrer la décrémentation
    function startDecrement(elementId, duration) {
        // Décrémenter toutes les secondes
        var countdownInterval = setInterval(function() {
            decrementValueById(elementId);
        }, 1000);

        // Arrêter la décrémentation après la durée spécifiée
        setTimeout(function() {
            clearInterval(countdownInterval); // Arrêter le décompte
            var toast = document.querySelector('.toast');
            toast.classList.remove('show');
            if (toast.dataset.redirect !== 'undefined') {
                window.location.replace(toast.dataset.redirect);
            }
        }, duration * 1000); // Convertir la durée en millisecondes
    }

    // Utilisation :
    startDecrement('compteur', 10); // Décrémenter monElement chaque seconde pendant 60 secondes
}

// Démarre la fonction start lorsque le DOM est chargé
document.addEventListener("DOMContentLoaded", start);