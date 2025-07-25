document.addEventListener('DOMContentLoaded', () => {

    const LESSON_STATES_KEY = 'lessonStates';
    const CHALLENGE_STATES_KEY = 'challengeStates';

    const getCompletionStates = (key) => {
        const states = localStorage.getItem(key);
        return states ? JSON.parse(states) : {};
    };

    const saveCompletionStates = (key, states) => {
        localStorage.setItem(key, JSON.stringify(states));
    };

    const initializeListPage = (storageKey, attributeName) => {
        const datasetKey = attributeName.replace('data-', '').replace(/-(\w)/g, (_, letter) => letter.toUpperCase());
        
        const cards = document.querySelectorAll(`.card[${attributeName}]`);
        if (cards.length === 0) return;

        const completedStates = getCompletionStates(storageKey);
        
        cards.forEach(card => {
            const itemId = card.dataset[datasetKey];
            if (itemId && completedStates[itemId]) {
                card.classList.add('completed');
            }
        });
    };

    const initializeDetailPage = (storageKey, attributeName, containerSelector) => {
        const nextButton = document.querySelector('.next-button');
        const container = document.querySelector(containerSelector);
        
        if (!nextButton || !container) return;

        const datasetKey = attributeName.replace('data-', '').replace(/-(\w)/g, (_, letter) => letter.toUpperCase());
        const itemId = container.dataset[datasetKey];
        if (!itemId) return;

        nextButton.addEventListener('click', () => {
            const states = getCompletionStates(storageKey);
            states[itemId] = true;
            saveCompletionStates(storageKey, states);
        });
    };

    initializeListPage(LESSON_STATES_KEY, 'data-lesson-id');
    initializeListPage(CHALLENGE_STATES_KEY, 'data-challenge-id');
    
    initializeDetailPage(LESSON_STATES_KEY, 'data-lesson-id', '.lesson-container');
    initializeDetailPage(CHALLENGE_STATES_KEY, 'data-challenge-id', '.challenge-container');
});