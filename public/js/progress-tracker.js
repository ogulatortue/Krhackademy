// js/progress-tracker.js
document.addEventListener('DOMContentLoaded', () => {

    /**
     * Applique la classe 'completed' aux cartes basées sur les données du serveur.
     */
    const applyProgressToUI = (progressData) => {
        const progress = progressData.data;
        if (!progress) return;

        const completedLessons = progress.lessons || [];
        const completedChallenges = progress.challenges || [];

        const allCards = document.querySelectorAll('.card');
        allCards.forEach(card => {
            const lessonId = card.dataset.lessonId;
            const challengeId = card.dataset.challengeId;

            // CORRECTION FINALE : On transforme l'ID de la carte (texte) en nombre avec parseInt()
            // pour le comparer à la liste d'IDs (nombres) venant de l'API.
            if ((lessonId && completedLessons.includes(parseInt(lessonId, 10))) || 
                (challengeId && completedChallenges.includes(parseInt(challengeId, 10)))) {
                card.classList.add('completed');
            }
        });
    };

    /**
     * Fait un appel à l'API pour récupérer la progression de l'utilisateur.
     */
    const loadUserProgress = async () => {
        try {
            const response = await fetch('/api/get-progress');
            if (response.ok) {
                const result = await response.json();
                if (result.status === 'success') {
                    applyProgressToUI(result);
                }
            } else {
                // Utilisateur non connecté, on ne fait rien, c'est normal.
            }
        } catch (error) {
            console.error('Erreur de chargement de la progression:', error);
        }
    };
    
    // --- DÉMARRAGE ---
    // S'exécute sur les pages qui ont des listes de cartes.
    if (document.querySelector('.box-container')) {
        loadUserProgress();
    }
});