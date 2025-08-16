document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('header');
    const main = document.querySelector('main');
    const menuIcon = document.querySelector('.fa-bars');
    const navBarMobile = document.getElementById('mobile-nav-menu');

    const searchToggleBtn = document.getElementById('search-toggle-btn');
    const filterControls = document.getElementById('filter-controls');
    const closeFilterBtn = document.getElementById('close-filter-btn');

    const profileToggleBtn = document.getElementById('profile-toggle-btn');
    const profileMenu = document.getElementById('profile-menu');
    const closeProfileBtn = document.getElementById('close-profile-btn');

    const leaderboardToggleBtn = document.getElementById('leaderboard-toggle-btn');
    const leaderboardMenu = document.getElementById('leaderboard-menu');
    const closeLeaderboardBtn = document.getElementById('close-leaderboard-btn');

    let initialMainPaddingTop;
    if (main) {
        initialMainPaddingTop = getComputedStyle(main).paddingTop;
    }

    const showAllToggleButtons = () => {
        if (menuIcon) menuIcon.classList.remove('hidden');
        if (searchToggleBtn) searchToggleBtn.classList.remove('hidden');
        if (profileToggleBtn) profileToggleBtn.classList.remove('hidden');
        if (leaderboardToggleBtn) leaderboardToggleBtn.classList.remove('hidden');
    }

    const closeSearchPanel = () => {
        if (filterControls && filterControls.classList.contains('open')) {
            main.style.paddingTop = initialMainPaddingTop;
            filterControls.classList.remove('open');
            showAllToggleButtons();
            if (searchToggleBtn) searchToggleBtn.focus();
            searchToggleBtn.setAttribute('aria-expanded', 'false');
            filterControls.setAttribute('aria-hidden', 'true');
        }
    };

    const closeProfileMenu = () => {
        if (profileMenu && profileMenu.classList.contains('open')) {
            profileMenu.classList.remove('open');
            showAllToggleButtons();
            if (profileToggleBtn) profileToggleBtn.focus();
            profileToggleBtn.setAttribute('aria-expanded', 'false');
            profileMenu.setAttribute('aria-hidden', 'true');
        }
    };

    const closeLeaderboardMenu = () => {
        if (leaderboardMenu && leaderboardMenu.classList.contains('open')) {
            leaderboardMenu.classList.remove('open');
            showAllToggleButtons();
            if (leaderboardToggleBtn) leaderboardToggleBtn.focus();
            leaderboardToggleBtn.setAttribute('aria-expanded', 'false');
            leaderboardMenu.setAttribute('aria-hidden', 'true');
        }
    };

    const closeMobileMenu = () => {
        if (navBarMobile && navBarMobile.classList.contains('open')) {
            navBarMobile.classList.remove('open');
            showAllToggleButtons();
            if (menuIcon) {
                menuIcon.focus();
                menuIcon.classList.remove('fa-times');
            }
            menuIcon.setAttribute('aria-expanded', 'false');
            navBarMobile.setAttribute('aria-hidden', 'true');
        }
    };

    function setupScrollEffects() {
        if (!header) return;
        const handleHeaderStyle = () => {
            if (window.scrollY > 20) {
                header.classList.add('header-active');
            } else {
                header.classList.remove('header-active');
            }
        };
        window.addEventListener('scroll', handleHeaderStyle);
        window.addEventListener('load', handleHeaderStyle);
    }

    function setupPanelToggles() {
        const panels = [{
            btn: menuIcon,
            panel: navBarMobile,
            closeBtn: null,
            onOpen: () => {
                if (menuIcon) menuIcon.classList.add('fa-times');
                setTimeout(() => navBarMobile.querySelector('a').focus(), 100);
            },
            onClose: closeMobileMenu
        }, {
            btn: searchToggleBtn,
            panel: filterControls,
            closeBtn: closeFilterBtn,
            onOpen: () => {
                const filterHeight = filterControls.offsetHeight;
                main.style.paddingTop = `calc(${initialMainPaddingTop} + ${filterHeight}px)`;
                setTimeout(() => document.getElementById('search-input').focus(), 100);
            },
            onClose: closeSearchPanel
        }, {
            btn: profileToggleBtn,
            panel: profileMenu,
            closeBtn: closeProfileBtn,
            onOpen: () => {
                setTimeout(() => profileMenu.querySelector('a').focus(), 100);
            },
            onClose: closeProfileMenu
        }, {
            btn: leaderboardToggleBtn,
            panel: leaderboardMenu,
            closeBtn: closeLeaderboardBtn,
            onOpen: () => {
                setTimeout(() => leaderboardMenu.querySelector('.leaderboard-item').focus(), 100);
            },
            onClose: closeLeaderboardMenu
        }];

        panels.forEach((p) => {
            if (!p.btn || !p.panel) return;

            p.btn.addEventListener('click', (event) => {
                event.stopPropagation();
                const wasOpen = p.panel.classList.contains('open');

                // Ferme tous les panneaux existants
                panels.forEach(otherPanel => otherPanel.onClose());

                if (!wasOpen) {
                    p.panel.classList.add('open');
                    
                    // Masque tous les boutons de bascule, y compris celui qui a été cliqué
                    panels.forEach(panelToHide => {
                        if (panelToHide.btn) panelToHide.btn.classList.add('hidden');
                    });
                    
                    // Une exception pour le menu hamburger qui change d'icône, si vous voulez le conserver
                    if (p.btn === menuIcon) {
                        menuIcon.classList.remove('hidden');
                    }

                    p.btn.setAttribute('aria-expanded', 'true');
                    p.panel.setAttribute('aria-hidden', 'false');
                    if (p.onOpen) p.onOpen();
                }
            });

            if (p.closeBtn) {
                p.closeBtn.addEventListener('click', p.onClose);
            }
        });

        document.addEventListener('click', (event) => {
            panels.forEach(p => {
                if (p.panel && p.panel.classList.contains('open') && !p.panel.contains(event.target) && !p.btn.contains(event.target)) {
                    p.onClose();
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                panels.forEach(p => p.onClose());
            }
        });

        window.addEventListener('scroll', () => {
            panels.forEach(p => {
                if (p.panel !== filterControls) {
                    p.onClose();
                }
            });
        });
    }

    setupScrollEffects();
    setupPanelToggles();
});