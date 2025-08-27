document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('header');
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
    
    const allPanels = [mobileNavPanel, searchPanel, profilePanel, leaderboardPanel];
    const actionButtons = [searchBtn, profileBtn, leaderboardBtn];

    const updateActionButtonsVisibility = () => {
        const isAnyPanelOpen = allPanels.some(p => p && p.classList.contains('open'));
        actionButtons.forEach(btn => {
            if (btn) {
                if (isAnyPanelOpen) {
                    btn.classList.add('hidden');
                } else {
                    btn.classList.remove('hidden');
                }
            }
        });
    };

    const togglePanel = (panelToOpen) => {
        allPanels.forEach(panel => {
            if (panel && panel !== panelToOpen) {
                panel.classList.remove('open');
            }
        });
        if (panelToOpen) {
            panelToOpen.classList.toggle('open');
        }
        mobileMenuBtn.classList.toggle('fa-times', mobileNavPanel && mobileNavPanel.classList.contains('open'));
        updateActionButtonsVisibility();
    };

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            togglePanel(mobileNavPanel);
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            togglePanel(searchPanel);
        });
    }

    if (profileBtn) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            togglePanel(profilePanel);
        });
    }

    if (leaderboardBtn) {
        leaderboardBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            togglePanel(leaderboardPanel);
        });
    }

    if (closeProfileBtn) {
        closeProfileBtn.addEventListener('click', () => togglePanel(null));
    }
    
    if (closeSearchBtn) {
        closeSearchBtn.addEventListener('click', () => togglePanel(null));
    }

    if (closeLeaderboardBtn) {
        closeLeaderboardBtn.addEventListener('click', () => togglePanel(null));
    }

    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            header.classList.add('header-active');
        } else {
            header.classList.remove('header-active');
        }
    });

    document.addEventListener('click', (e) => {
        const isClickInsideHeader = header.contains(e.target);
        const isClickInsidePanel = allPanels.some(p => p && p.contains(e.target));
        if (!isClickInsideHeader && !isClickInsidePanel) {
            allPanels.forEach(p => p && p.classList.remove('open'));
            if (mobileMenuBtn) {
                mobileMenuBtn.classList.remove('fa-times');
            }
            updateActionButtonsVisibility();
        }
    });
});