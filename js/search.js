document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');
    const difficultyFilter = document.getElementById('difficulty-filter');
    const challengeCards = document.querySelectorAll('.challenge-card');
    const categorySections = document.querySelectorAll('.category-section');
    function filterChallenges() {
        const searchText = searchInput.value.toLowerCase().trim();
        const selectedCategory = categoryFilter.value;
        const selectedDifficulty = difficultyFilter.value;

        challengeCards.forEach(card => {
            const cardName = card.querySelector('h4').textContent.toLowerCase();
            const cardCategory = card.dataset.category || '';
            const cardDifficulty = card.dataset.difficulty || '';
            const nameMatch = cardName.includes(searchText);
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
    }

    searchInput.addEventListener('input', filterChallenges);
    categoryFilter.addEventListener('change', filterChallenges);
    difficultyFilter.addEventListener('change', filterChallenges);
});