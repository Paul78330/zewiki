function start() {
    // On récupère les trois éléments (deux panneaux et séparateur)
    const separator = document.getElementById('separator');
    const leftPanel = document.getElementById('leftpanel');
    const rightPanel = document.getElementById('rightpanel');

    // Récupérer la dernière taille modifiée par l'utilisateur du panneau gauche
    let leftpanelsize = localStorage.getItem('leftPanelSize');

    // Utiliser cette taille pour afficher le panneau gauche et droit 
    leftPanel.style.width = `${leftpanelsize}%`;
    rightPanel.style.width = `${100 - leftpanelsize}%`;

    // -----  Gestion du changement de répartition de taille entre les deux panneaux ( gauche et droite) en déplacant le séparateur

    // On ajoute un listener sur le clic au niveau du séparateur
    separator.addEventListener('mousedown', function(e) {
        e.preventDefault();
        document.addEventListener('mousemove', startResize);
        document.addEventListener('mouseup', stopResize);
    });

    function startResize(e) {
        const newWidth = e.clientX / window.innerWidth * 100;
        leftPanel.style.width = `${newWidth}%`;
        rightPanel.style.width = `${100 - newWidth}%`;
    }

    function stopResize(e) {
        // sauvegarder la nouvelle taille définie
        localStorage.setItem('leftPanelSize', e.clientX / window.innerWidth * 100);
        
        document.removeEventListener('mousemove', startResize);
        document.removeEventListener('mouseup', stopResize);
    }

    // Évitez de capturer le mouseup par le séparateur lui-même
    separator.addEventListener('mouseup', function(e) {
        e.stopPropagation();
    });

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




