document.addEventListener('DOMContentLoaded', () => {

    const LESSON_STATES_KEY = 'lessonStates';

    const getLessonStates = () => {
        const states = localStorage.getItem(LESSON_STATES_KEY);
        return states ? JSON.parse(states) : {};
    };

    const saveLessonStates = (states) => {
        localStorage.setItem(LESSON_STATES_KEY, JSON.stringify(states));
    };

    const initializeLessonListPage = () => {
        const lessonCards = document.querySelectorAll('.card');
        if (lessonCards.length === 0) {
            return; 
        }

        const lessonStates = getLessonStates();
        
        lessonCards.forEach(card => {
            const lessonId = card.dataset.lessonId;
            if (lessonId && lessonStates[lessonId]) {
                card.classList.add('completed');
            }
        });
    };

    const initializeLessonDetailPage = () => {
        const nextButton = document.querySelector('.next-button');
        const lessonContainer = document.querySelector('.lesson-container');
        
        if (!nextButton || !lessonContainer) {
            return;
        }

        const lessonId = lessonContainer.dataset.lessonId;
        if (!lessonId) return;

        nextButton.addEventListener('click', (event) => {
            event.preventDefault();
            
            const lessonStates = getLessonStates();
            lessonStates[lessonId] = true;
            saveLessonStates(lessonStates);

            window.location.href = nextButton.href;
        });
    };

    initializeLessonListPage();
    initializeLessonDetailPage();
});