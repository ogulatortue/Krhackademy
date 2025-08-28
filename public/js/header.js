window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.scrollTo(0, 0);
    }
});

document.addEventListener('DOMContentLoaded', () => {
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }
    window.scrollTo(0, 0);

    const header = document.querySelector('header');
    const mainContent = document.querySelector('main');
    const mobileMenuBtn = document.querySelector('header .fa-bars');
    const searchBtn = document.getElementById('search-toggle-btn');
    const profileBtn = document.getElementById('profile-toggle-btn');
    const leaderboardBtn = document.getElementById('leaderboard-toggle-btn');
    const mobileNavPanel = document.getElementById('mobile-nav-menu');
    const searchPanel = document.getElementById('filter-controls');
    const profilePanel = document.getElementById('profile-menu');
    const leaderboardPanel = document.getElementById('leaderboard-menu');
    const closeProfileBtn = document.getElementById('close-profile-btn');
    const closeSearchBtn = document.getElementById('close-filter-btn');
    const closeLeaderboardBtn = document.getElementById('close-leaderboard-btn');
    const searchInput = document.getElementById('search-input');

    const allPanels = [mobileNavPanel, searchPanel, profilePanel, leaderboardPanel].filter(Boolean);
    const actionButtons = [searchBtn, profileBtn, leaderboardBtn].filter(Boolean);
    const baseMainPaddingInRem = 6;
    let lastFocusedElement = null;

    const initializeMainPadding = () => {
        if (mainContent) {
            const rootFontSize = parseFloat(getComputedStyle(document.documentElement).fontSize);
            const basePaddingPx = baseMainPaddingInRem * rootFontSize;
            mainContent.style.paddingTop = `${basePaddingPx}px`;
        }
    };

    const resetSearch = () => {
        if (searchInput) {
            searchInput.value = '';
        }
        document.querySelectorAll('.custom-select-wrapper').forEach(wrapper => {
            const trigger = wrapper.querySelector('.custom-select-trigger');
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const firstOption = wrapper.querySelector('.custom-option');
            if (trigger && hiddenInput && firstOption) {
                trigger.textContent = firstOption.textContent;
                hiddenInput.value = firstOption.dataset.value;
            }
        });
        if (searchInput) {
            searchInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    };

    const adjustMainPaddingForSearch = () => {
        if (searchPanel && searchPanel.classList.contains('open')) {
            const rootFontSize = parseFloat(getComputedStyle(document.documentElement).fontSize);
            const basePaddingPx = baseMainPaddingInRem * rootFontSize;
            const panelHeight = searchPanel.offsetHeight;
            mainContent.style.paddingTop = `${basePaddingPx + panelHeight + 10}px`;
        } else {
            initializeMainPadding();
        }
    };

    const updateActionButtonsVisibility = () => {
        const isAnyPanelOpen = allPanels.some(p => p && p.classList.contains('open'));
        actionButtons.forEach(btn => {
            if (btn) {
                btn.classList.toggle('hidden', isAnyPanelOpen);
            }
        });
    };

    const closeAllPanels = () => {
        const searchWasOpen = searchPanel && searchPanel.classList.contains('open');
        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
        allPanels.forEach(p => {
            if (p) {
                p.classList.remove('open');
                p.setAttribute('aria-hidden', 'true');
            }
        });
        updateActionButtonsVisibility();
        
        if (searchWasOpen) {
            setTimeout(adjustMainPaddingForSearch, 50);
            resetSearch();
        }
        
        if (mobileMenuBtn) {
            mobileMenuBtn.classList.remove('fa-times');
        }
    };

    const togglePanel = (panelToOpen) => {
        const wasOpen = panelToOpen && panelToOpen.classList.contains('open');
        const searchWasOpen = searchPanel && searchPanel.classList.contains('open');

        if (!wasOpen) {
            lastFocusedElement = document.activeElement;
        }

        allPanels.forEach(panel => {
            if (panel && panel !== panelToOpen) {
                panel.classList.remove('open');
                panel.setAttribute('aria-hidden', 'true');
            }
        });

        if (panelToOpen) {
            const isOpen = panelToOpen.classList.toggle('open');
            panelToOpen.setAttribute('aria-hidden', !isOpen);

            if (isOpen && panelToOpen === searchPanel && searchInput) {
                setTimeout(() => {
                    searchInput.focus();
                }, 100); 
            }
        }

        mobileMenuBtn.classList.toggle('fa-times', mobileNavPanel && mobileNavPanel.classList.contains('open'));
        updateActionButtonsVisibility();
        
        if (panelToOpen === searchPanel) {
            setTimeout(adjustMainPaddingForSearch, 50);
        }

        if (searchWasOpen && panelToOpen !== searchPanel) {
            resetSearch();
        }
        
        if (wasOpen && panelToOpen === searchPanel) {
            resetSearch();
        }
    };

    if (mobileMenuBtn) { mobileMenuBtn.addEventListener('click', (e) => { e.stopPropagation(); togglePanel(mobileNavPanel); }); }
    if (searchBtn) { searchBtn.addEventListener('click', (e) => { e.stopPropagation(); togglePanel(searchPanel); }); }
    if (profileBtn) { profileBtn.addEventListener('click', (e) => { e.stopPropagation(); togglePanel(profilePanel); }); }
    if (leaderboardBtn) { leaderboardBtn.addEventListener('click', (e) => { e.stopPropagation(); togglePanel(leaderboardPanel); }); }

    if (closeProfileBtn) {
        closeProfileBtn.addEventListener('click', () => {
            closeAllPanels();
        });
    }
    if (closeSearchBtn) {
        closeSearchBtn.addEventListener('click', () => {
            closeAllPanels();
        });
    }
    if (closeLeaderboardBtn) {
        closeLeaderboardBtn.addEventListener('click', () => {
            closeAllPanels();
        });
    }

    window.addEventListener('scroll', () => {
        header.classList.toggle('header-active', window.scrollY > 20);
    });

    document.addEventListener('click', (e) => {
        const isClickInsideHeader = header.contains(e.target);
        const isClickInsidePanel = allPanels.some(p => p && p.contains(e.target));
        if (!isClickInsideHeader && !isClickInsidePanel) {
            const searchWasOpen = searchPanel && searchPanel.classList.contains('open');
            closeAllPanels();
            if (searchWasOpen) {
                adjustMainPaddingForSearch();
            }
        }
    });

    initializeMainPadding();
});