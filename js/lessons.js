document.addEventListener('DOMContentLoaded', () => {

    const lessonContainers = document.querySelectorAll('.box_container');

    function setupStaggeredAnimation() {
        if (lessonContainers.length === 0) return;

        lessonContainers.forEach(container => {
            const lessons = container.querySelectorAll('a.lesson-card');
            lessons.forEach((lesson, index) => {
                lesson.style.animationDelay = `${(index + 1) * 0.1}s`;
                lesson.style.animationFillMode = 'backwards'; 
            });
        });
    }

    const LESSON_STATES_KEY = 'lessonStates';

    const getLessonStates = () => {
        const states = localStorage.getItem(LESSON_STATES_KEY);
        return states ? JSON.parse(states) : {};
    };

    const saveLessonStates = (states) => {
        localStorage.setItem(LESSON_STATES_KEY, JSON.stringify(states));
    };

    const updateCardAppearance = (card, isCompleted) => {
        const icon = card.querySelector('.checkbox-icon');
        if (!icon) return;

        card.classList.toggle('completed-lesson', isCompleted);
        
        icon.classList.toggle('completed', isCompleted);
        icon.classList.toggle('not-completed', !isCompleted);
        
        icon.classList.toggle('fa-check-circle', isCompleted);
        icon.classList.toggle('fa-times-circle', !isCompleted);
    };

    const initializeCards = () => {
        const lessonStates = getLessonStates();
        const cards = document.querySelectorAll('.lesson-card');

        cards.forEach(card => {
            const lessonId = card.dataset.lessonId;
            if (lessonId) {
                const isCompleted = lessonStates[lessonId] || false;
                updateCardAppearance(card, isCompleted);
            }
        });
    };

    const handleCheckClick = (event) => {
        const checkBox = event.target.closest('.check_box');
        if (!checkBox) return;
        
        event.preventDefault();
        event.stopPropagation();

        const card = checkBox.closest('.lesson-card');
        const lessonId = card.dataset.lessonId;

        if (!lessonId) return;

        const lessonStates = getLessonStates();
        const newStatus = !lessonStates[lessonId];
        
        lessonStates[lessonId] = newStatus;
        saveLessonStates(lessonStates);
        
        updateCardAppearance(card, newStatus);
    };
    
    setupStaggeredAnimation(); 
    initializeCards();
    document.querySelector('.lessons-main-content').addEventListener('click', handleCheckClick);
});