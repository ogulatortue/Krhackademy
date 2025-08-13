// js/progress-tracker.js
document.addEventListener('DOMContentLoaded', () => {

    /**
     * Applique la classe 'completed' aux cartes basées sur les données du serveur.
     */
    const applyProgressToUI = (progress) => {
        if (!progress) return;

        const allCards = document.querySelectorAll('.card');
        allCards.forEach(card => {
            const lessonId = card.dataset.lessonId;
            const challengeId = card.dataset.challengeId;

            // Vérifie si la carte correspond à un élément complété
            if ((lessonId && progress.lessons?.includes(lessonId)) || 
                (challengeId && progress.challenges?.includes(challengeId))) {
                card.classList.add('completed');
            }
        });
    };

    /**
     * Fait un appel à l'API pour récupérer la progression de l'utilisateur.
     */
    const loadUserProgress = async () => {
        try {
            // Le chemin vers l'API est relatif à la page HTML qui charge le script.
            const response = await fetch('./back-end/get_progress.php');
            if (response.ok) {
                const progress = await response.json();
                applyProgressToUI(progress);
            } else {
                // Si la réponse n'est pas OK (ex: 403 Forbidden car non connecté), on ne fait rien.
                // L'utilisateur verra simplement tout comme "non complété".
                console.log('Utilisateur non connecté ou erreur API.');
            }
        } catch (error) {
            console.error('Erreur de chargement de la progression:', error);
        }
    };

    /**
     * Configure le bouton "Suivant" sur les pages de leçons pour sauvegarder la progression.
     */
    const initializeLessonDetailPage = () => {
        const nextButton = document.querySelector('.next-button');
        const lessonContainer = document.querySelector('.lesson-container'); // Votre conteneur principal sur la page de leçon

        if (!nextButton || !lessonContainer) return;

        const lessonId = lessonContainer.dataset.lessonId;
        if (!lessonId) return;

        nextButton.addEventListener('click', async (event) => {
            // Empêche le bouton (si c'est un lien <a>) de naviguer immédiatement
            event.preventDefault();
            const originalHref = event.currentTarget.href;

            // On sauvegarde la progression en arrière-plan
            try {
                await fetch('./back-end/mark_lesson_complete.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: lessonId })
                });
            } catch (error) {
                console.error('Impossible de sauvegarder la progression de la leçon:', error);
            } finally {
                // Une fois la sauvegarde tentée, on redirige l'utilisateur
                window.location.href = originalHref;
            }
        });
    };


    // --- DÉMARRAGE ---

    // Sur les pages listes (lessons.html, challenges.html), on charge la progression
    if (document.querySelector('.box-container')) {
        loadUserProgress();
    }
    
    // Sur les pages détail de leçons (lessons/forensics-intro.html, etc.)
    if (document.querySelector('.lesson-container')) {
        initializeLessonDetailPage();
    }
});