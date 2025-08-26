document.addEventListener('DOMContentLoaded', () => {

    const applyProgressToUI = (progressData) => {
        const progress = progressData.data;
        if (!progress) return;

        const completedLessons = progress.lessons || [];
        const completedChallenges = progress.challenges || [];

        const allCards = document.querySelectorAll('.card');
        allCards.forEach(card => {
            const lessonId = card.dataset.lessonId;
            const challengeId = card.dataset.challengeId;

            if ((lessonId && completedLessons.includes(parseInt(lessonId, 10))) ||
                (challengeId && completedChallenges.includes(parseInt(challengeId, 10)))) {
                card.classList.add('completed');
            }
        });
    };

    const loadUserProgress = async () => {
        try {
            const response = await fetch('/api/get-progress');
            if (response.ok) {
                const result = await response.json();
                if (result.status === 'success') {
                    applyProgressToUI(result);
                }
            } else {
                console.error(`Erreur HTTP inattendue: ${response.status} ${response.statusText}`);
            }
        } catch (error) {
            console.error('Erreur de chargement de la progression (erreur réseau):', error);
        }
    };

    if (document.querySelector('.box-container')) {
        const isLoggedIn = document.body.dataset.userLoggedIn === 'true';

        if (isLoggedIn) {
            loadUserProgress();
        } else {
            console.log("Utilisateur non connecté, la récupération de la progression est ignorée.");
        }
    }
});