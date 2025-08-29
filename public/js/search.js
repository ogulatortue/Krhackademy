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
    const noResultsMessage = document.getElementById('no-results-message');

    const closeAllSelects = (exceptThisOne = null) => {
        document.querySelectorAll('.custom-select.open').forEach(select => {
            if (select !== exceptThisOne) {
                select.classList.remove('open');
                if (select.nextElementSibling) {
                    select.nextElementSibling.classList.remove('open');
                }
            }
        });
    };

    const setupFilterSelects = () => {
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            const select = wrapper.querySelector('.custom-select');
            const trigger = wrapper.querySelector('.custom-select-trigger');
            const optionsContainer = wrapper.querySelector('.custom-options');
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            if (!select || !trigger || !optionsContainer || !hiddenInput) return;

            select.addEventListener('click', (event) => {
                event.stopPropagation();
                const wasOpen = select.classList.contains('open');
                closeAllSelects(select);
                if (!wasOpen) {
                    select.classList.add('open');
                    optionsContainer.classList.add('open');
                }
            });

            optionsContainer.addEventListener('click', (event) => {
                const option = event.target.closest('.custom-option');
                if (option) {
                    trigger.textContent = option.textContent;
                    hiddenInput.value = option.dataset.value;
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                    closeAllSelects();
                }
            });
        });
    };

    const populateFilters = () => {
        const categoryOptionsContainer = document.querySelector('[data-select-id="category-filter"]')?.nextElementSibling;
        const difficultyOptionsContainer = document.querySelector('[data-select-id="difficulty-filter"]')?.nextElementSibling;

        if (categoryOptionsContainer) {
            const categories = new Set();
            categorySections.forEach(section => {
                const title = section.querySelector('.category-title')?.textContent.trim();
                if (title) categories.add(title);
            });
            Array.from(categoryOptionsContainer.querySelectorAll('.custom-option:not([data-value="all"])')).forEach(el => el.remove());
            categories.forEach(category => {
                const option = document.createElement('div');
                option.className = 'custom-option';
                option.dataset.value = category;
                option.textContent = category;
                categoryOptionsContainer.appendChild(option);
            });
        }

        if (difficultyOptionsContainer) {
            const allDifficulties = new Map([
                ['easy', 'Débutant'], ['medium', 'Initié'],
                ['hard', 'Avancé'], ['expert', 'Expert']
            ]);
            Array.from(difficultyOptionsContainer.querySelectorAll('.custom-option:not([data-value="all"])')).forEach(el => el.remove());
            allDifficulties.forEach((text, key) => {
                const option = document.createElement('div');
                option.className = 'custom-option';
                option.dataset.value = key;
                option.textContent = text;
                difficultyOptionsContainer.appendChild(option);
            });
        }
    };

    const filterItems = () => {
        const search = searchInput.value.toLowerCase().trim();
        const category = categoryFilter.value;
        const difficulty = difficultyFilter.value;
        const completion = completionFilter.value;
        let hasVisibleCards = false;

        cards.forEach(card => {
            const cardCategory = card.closest('.category-section')?.querySelector('.category-title')?.textContent.trim() || '';
            const cardDifficulty = card.dataset.difficulty || 'none';
            const cardTitle = card.querySelector('h4')?.textContent.toLowerCase() || '';
            const isCompleted = card.classList.contains('completed');

            const categoryMatch = (category === 'all' || cardCategory === category);
            const difficultyMatch = (difficulty === 'all' || cardDifficulty === difficulty);
            const completionMatch = (completion === 'all') || (completion === 'completed' && isCompleted) || (completion === 'not-completed' && !isCompleted);
            const searchMatch = cardTitle.includes(search);

            if (categoryMatch && difficultyMatch && completionMatch && searchMatch) {
                card.style.display = 'flex';
                hasVisibleCards = true;
            } else {
                card.style.display = 'none';
            }
        });

        categorySections.forEach(section => {
            const visibleCardsInSection = section.querySelectorAll('.card[style*="display: flex"]');
            section.style.display = visibleCardsInSection.length > 0 ? 'block' : 'none';
        });

        if (noResultsMessage) {
            noResultsMessage.style.display = hasVisibleCards ? 'none' : 'block';
        }
    };

    const resetSearchAndFilters = () => {
        searchInput.value = '';
        clearSearchBtn.classList.remove('visible');

        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const trigger = wrapper.querySelector('.custom-select-trigger');
            const defaultOption = wrapper.querySelector('.custom-option[data-value="all"]');
            if (hiddenInput && trigger && defaultOption) {
                hiddenInput.value = 'all';
                trigger.textContent = defaultOption.textContent;
            }
        });
        filterItems();
    };
    
    cards.forEach(card => {
        const diffTag = card.querySelector('.difficulty-tag');
        if (diffTag) {
            const valueClass = Array.from(diffTag.classList).find(c => c.startsWith('difficulty-') && c !== 'difficulty-tag');
            if (valueClass) card.dataset.difficulty = valueClass.replace('difficulty-', '');
        }
    });

    setupFilterSelects();

    const allFilters = [searchInput, categoryFilter, difficultyFilter, completionFilter];
    allFilters.forEach(filter => {
        if (filter) {
            const eventType = filter.tagName === 'INPUT' && (filter.type === 'search' || filter.type === 'text') ? 'input' : 'change';
            filter.addEventListener(eventType, filterItems);
        }
    });

    searchInput.addEventListener('input', () => {
        clearSearchBtn.classList.toggle('visible', searchInput.value.length > 0);
    });
    clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input', { bubbles: true }));
    });

    if (cards.length > 0) {
        populateFilters();
    }
    
    document.addEventListener('closeSearchPanel', resetSearchAndFilters);
    document.addEventListener('click', () => closeAllSelects());
});