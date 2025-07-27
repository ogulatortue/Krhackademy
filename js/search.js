document.addEventListener('DOMContentLoaded', () => {
    const searchPanel = document.getElementById('filter-controls');
    if (!searchPanel) return;

    const searchInput = document.getElementById('search-input');
    const clearSearchBtn = document.getElementById('clear-search-btn');
    const categoryFilter = document.getElementById('category-filter');
    const difficultyFilter = document.getElementById('difficulty-filter');
    const completionFilter = document.getElementById('completion-filter');
    const cards = document.querySelectorAll('.main .card');
    const categorySections = document.querySelectorAll('.category-section');
    const noResultsMessage = document.getElementById('no-results-message') || document.querySelector('.no-results');

    // Étape 1 : Pré-traiter les cartes pour extraire la difficulté
    cards.forEach(card => {
        const diffTag = card.querySelector('.difficulty-tag');
        if (diffTag) {
            const valueClass = Array.from(diffTag.classList).find(c => c.startsWith('difficulty-'));
            if (valueClass) {
                card.dataset.difficulty = valueClass.replace('difficulty-', '');
            }
        }
    });
    
    // Étape 2 : Logique pour gérer les menus déroulants personnalisés
    function setupFilterSelects() {
        const customSelects = searchPanel.querySelectorAll('.custom-select');

        customSelects.forEach(select => {
            const trigger = select.querySelector('.custom-select-trigger');
            const options = select.nextElementSibling;
            const hiddenInput = document.getElementById(select.dataset.selectId);

            select.addEventListener('click', (event) => {
                event.stopPropagation();
                const wasOpen = select.classList.contains('open');
                customSelects.forEach(s => {
                    s.classList.remove('open');
                    s.nextElementSibling.classList.remove('open');
                });
                if (!wasOpen) {
                    select.classList.add('open');
                    options.classList.add('open');
                }
            });

            options.addEventListener('click', (event) => {
                const targetOption = event.target.closest('.custom-option');
                if (targetOption) {
                    trigger.textContent = targetOption.textContent;
                    hiddenInput.value = targetOption.dataset.value;
                    hiddenInput.dispatchEvent(new Event('change'));
                    select.classList.remove('open');
                    options.classList.remove('open');
                }
            });
        });
        
        document.addEventListener('click', () => {
            customSelects.forEach(select => {
                select.classList.remove('open');
                select.nextElementSibling.classList.remove('open');
            });
        });
    }

    // Étape 3 : Remplir les filtres avec les bonnes options
    function populateCustomFilters() {
        const categoryOptionsContainer = document.querySelector('[data-select-id="category-filter"]').nextElementSibling;
        const difficultyOptionsContainer = document.querySelector('[data-select-id="difficulty-filter"]').nextElementSibling;

        const categories = new Set();
        categorySections.forEach(section => {
            const title = section.querySelector('.category-title')?.textContent.trim();
            if (title) categories.add(title);
        });
        categories.forEach(category => {
            const option = document.createElement('div');
            option.classList.add('custom-option');
            option.dataset.value = category;
            option.textContent = category;
            categoryOptionsContainer.appendChild(option);
        });

        const allDifficulties = new Map([
            ['easy', 'Débutant'],
            ['medium', 'Initié'],
            ['hard', 'Avancé'],
            ['expert', 'Expert']
        ]);
        allDifficulties.forEach((text, value) => {
            const option = document.createElement('div');
            option.classList.add('custom-option');
            option.dataset.value = value;
            option.textContent = text;
            difficultyOptionsContainer.appendChild(option);
        });
    }

    // Étape 4 : La fonction de filtrage
    function filterItems() {
        const selectedDifficulty = difficultyFilter.value;
        const selectedCategory = categoryFilter.value;
        const searchText = searchInput.value.toLowerCase().trim();
        const selectedCompletion = completionFilter.value;

        let hasVisibleCards = false;

        cards.forEach(card => {
            const cardDifficulty = card.dataset.difficulty || 'none';
            const cardCategory = card.closest('.category-section')?.querySelector('.category-title')?.textContent.trim() || '';
            const cardName = card.querySelector('h4')?.textContent.toLowerCase() || '';
            const isCompleted = card.classList.contains('completed');

            const difficultyMatch = (selectedDifficulty === 'all' || cardDifficulty === selectedDifficulty);
            const categoryMatch = (selectedCategory === 'all' || cardCategory === selectedCategory);
            const nameMatch = cardName.includes(searchText);
            const completionMatch = (selectedCompletion === 'all') ||
                                  (selectedCompletion === 'completed' && isCompleted) ||
                                  (selectedCompletion === 'not-completed' && !isCompleted);

            if (nameMatch && categoryMatch && difficultyMatch && completionMatch) {
                card.style.display = 'flex';
                hasVisibleCards = true;
            } else {
                card.style.display = 'none';
            }
        });

        categorySections.forEach(section => {
            const visibleCards = section.querySelectorAll('.card[style*="display: flex"]');
            section.style.display = (visibleCards.length > 0) ? 'block' : 'none';
        });

        if (noResultsMessage) {
            noResultsMessage.style.display = hasVisibleCards ? 'none' : 'block';
        }
    }
    
    // Étape 5 : Initialisation
    if (cards.length > 0) {
        populateCustomFilters();
        setupFilterSelects();

        searchInput.addEventListener('input', () => {
            filterItems();
            clearSearchBtn.classList.toggle('visible', searchInput.value.length > 0);
        });

        clearSearchBtn.addEventListener('click', () => {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        });

        // MODIFICATION FINALE : Écouteurs d'événements attachés individuellement
        if (categoryFilter) {
            categoryFilter.addEventListener('change', filterItems);
        }
        if (difficultyFilter) {
            difficultyFilter.addEventListener('change', filterItems);
        }
        if (completionFilter) {
            completionFilter.addEventListener('change', filterItems);
        }
    }
});