document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const clearSearchBtn = document.getElementById('clear-search-btn');
    const categoryFilter = document.getElementById('category-filter');
    const difficultyFilter = document.getElementById('difficulty-filter');
    const challengeCards = document.querySelectorAll('.challenge-card');
    const categorySections = document.querySelectorAll('.category-section');
    const noResultsMessage = document.getElementById('no-results-message');

    function populateCustomFilters() {
        const categoryOptionsContainer = document.querySelector('[data-select-id="category-filter"]').nextElementSibling;
        const difficultyOptionsContainer = document.querySelector('[data-select-id="difficulty-filter"]').nextElementSibling;

        const categories = new Set();
        challengeCards.forEach(card => {
            if (card.dataset.category) {
                categories.add(card.dataset.category);
            }
        });
        
        categories.forEach(category => {
            const option = document.createElement('div');
            option.classList.add('custom-option');
            option.dataset.value = category;
            option.textContent = category;
            categoryOptionsContainer.appendChild(option);
        });

        const difficulties = {
            'debutant': 'Débutant',
            'intermediaire': 'Intermédiaire',
            'avance': 'Avancé'
        };

        for (const [value, text] of Object.entries(difficulties)) {
             const existingCard = document.querySelector(`.challenge-card[data-difficulty='${value}']`);
             if (existingCard) {
                 const option = document.createElement('div');
                 option.classList.add('custom-option');
                 option.dataset.value = value;
                 option.textContent = text;
                 difficultyOptionsContainer.appendChild(option);
             }
        }
    }

    function filterChallenges() {
        const searchText = searchInput.value.toLowerCase().trim();
        const selectedCategory = categoryFilter.value;
        const selectedDifficulty = difficultyFilter.value;

        challengeCards.forEach(card => {
            const cardName = card.querySelector('h4').textContent.toLowerCase();
            const cardCategory = card.dataset.category || '';
            const cardDifficulty = card.dataset.difficulty || '';

            const nameMatch = cardName.split(' ').some(word => word.startsWith(searchText));
            const categoryMatch = (selectedCategory === 'all' || cardCategory === selectedCategory);
            const difficultyMatch = (selectedDifficulty === 'all' || cardDifficulty === selectedDifficulty);

            if (nameMatch && categoryMatch && difficultyMatch) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });

        categorySections.forEach(section => {
            const visibleCards = section.querySelectorAll('.challenge-card[style*="display: flex"]');
            
            if (visibleCards.length > 0) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });

        const anyVisibleCards = document.querySelector('.challenge-card[style*="display: flex"]');
        if (anyVisibleCards) {
            noResultsMessage.style.display = 'none';
        } else {
            noResultsMessage.style.display = 'block';
        }
    }
    
    populateCustomFilters();

    searchInput.addEventListener('input', () => {
        filterChallenges();
        if (searchInput.value.length > 0) {
            clearSearchBtn.classList.add('visible');
        } else {
            clearSearchBtn.classList.remove('visible');
        }
    });

    clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
    });

    categoryFilter.addEventListener('change', filterChallenges);
    difficultyFilter.addEventListener('change', filterChallenges);
});