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



// -----  Gestion de  l'affichage du body pour qu'il soit toujours juste en dessous de la navbar dont la hauteur varie lorsqu'on est en mobile et on clique le menu hamburger ----- 
// Adapte le padding-top au début (évite d'avoir a indiquer une valeur initiale )
adjustPaddingTop();
// fonction qui adapte la taille du padding top du body en fonction de la hauteur de la navbar ( lorsqu'on clique sur le menu hamburger la taille augmente)
function adjustPaddingTop() {
    const navbarHeight = document.querySelector('.navbar').offsetHeight;
    document.body.style.paddingTop = navbarHeight + 'px';
}



// Creation d'un listener qui ecoute le clic sur le menu hamburger
document.querySelector('.navbar-toggler').addEventListener('click', function () {
setTimeout(adjustPaddingTop, 300); // Délai pour laisser le temps à l'animation de se terminer
});



// -----  Gestion du changement de répartition de taille entre les deux panneaux ( gauche et droite) en déplacant le séparateur

// on récupere les trois éléments ( deux panneaux et séparateur)
const sparator = document.getElementById('separator');
const colonne1 = document.getElementById('leftpanel');
const colonne2 = document.getElementById('rightpanel');

// on ajoute un listener sur le clique au niveau du séparateur
sparator.addEventListener('mousedown', function(e) {
    e.preventDefault();
    document.addEventListener('mousemove', startResize);
    document.addEventListener('mouseup', stopResize);
});

function startResize(e) {
    const newWidth = e.clientX / window.innerWidth * 100;
    colonne1.style.width = `${newWidth}%`;
    colonne2.style.width = `${100 - newWidth}%`;
}

function stopResize() {
    document.removeEventListener('mousemove', startResize);
    document.removeEventListener('mouseup', stopResize);
}