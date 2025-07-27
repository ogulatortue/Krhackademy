document.addEventListener('DOMContentLoaded', function () {
    const header = document.querySelector('header');
    const main = document.querySelector('main');
    const menuIcon = document.querySelector('.fa-bars');
    const navBar = document.querySelector('.nav-bar');
    
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
        if(searchToggleBtn) searchToggleBtn.classList.remove('hidden');
        if(profileToggleBtn) profileToggleBtn.classList.remove('hidden');
        if(leaderboardToggleBtn) leaderboardToggleBtn.classList.remove('hidden');
    }

    const closeSearchPanel = () => {
        if (filterControls && filterControls.classList.contains('open')) {
            main.style.paddingTop = initialMainPaddingTop;
            filterControls.classList.remove('open');
            showAllToggleButtons();
            searchToggleBtn.setAttribute('aria-expanded', 'false');
            filterControls.setAttribute('aria-hidden', 'true');
        }
    };

    const closeProfileMenu = () => {
        if (profileMenu && profileMenu.classList.contains('open')) {
            profileMenu.classList.remove('open');
            showAllToggleButtons();
            profileToggleBtn.setAttribute('aria-expanded', 'false');
            profileMenu.setAttribute('aria-hidden', 'true');
        }
    };

    const closeLeaderboardMenu = () => {
        if (leaderboardMenu && leaderboardMenu.classList.contains('open')) {
            leaderboardMenu.classList.remove('open');
            showAllToggleButtons();
            leaderboardToggleBtn.setAttribute('aria-expanded', 'false');
            leaderboardMenu.setAttribute('aria-hidden', 'true');
        }
    };

    function setupMobileNavigation() {
        if (!menuIcon || !navBar) return;
        const closeMobileMenu = () => {
            navBar.classList.remove('open');
            menuIcon.classList.remove('fa-times');
        };
        menuIcon.addEventListener('click', (event) => {
            event.stopPropagation();
            navBar.classList.toggle('open');
            menuIcon.classList.toggle('fa-times');
        });
        document.addEventListener('click', (event) => {
            if (navBar.classList.contains('open') && !navBar.contains(event.target) && !menuIcon.contains(event.target)) {
                closeMobileMenu();
            }
        });
        window.addEventListener('scroll', closeMobileMenu);
    }

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
        const panels = [
            {
                btn: searchToggleBtn,
                panel: filterControls,
                closeBtn: closeFilterBtn,
                onOpen: () => {
                    const filterHeight = filterControls.offsetHeight;
                    main.style.paddingTop = `calc(${initialMainPaddingTop} + ${filterHeight}px)`;
                    setTimeout(() => document.getElementById('search-input').focus(), 100);
                },
                onClose: closeSearchPanel
            },
            {
                btn: profileToggleBtn,
                panel: profileMenu,
                closeBtn: closeProfileBtn,
                onOpen: () => {
                     setTimeout(() => profileMenu.querySelector('a').focus(), 100);
                },
                onClose: closeProfileMenu
            },
            {
                btn: leaderboardToggleBtn,
                panel: leaderboardMenu,
                closeBtn: closeLeaderboardBtn,
                onOpen: () => {
                    setTimeout(() => leaderboardMenu.querySelector('.leaderboard-item').focus(), 100);
                },
                onClose: closeLeaderboardMenu
            }
        ];
    
        panels.forEach((p, currentIndex) => {
            if (!p.btn || !p.panel || !p.closeBtn) return;
    
            p.btn.addEventListener('click', (event) => {
                event.stopPropagation();

                panels.forEach((otherPanel, otherIndex) => {
                    if (currentIndex !== otherIndex) {
                        otherPanel.onClose();
                    }
                });
    
                p.panel.classList.add('open');
                
                panels.forEach(panelToHide => {
                    if (panelToHide.btn) panelToHide.btn.classList.add('hidden');
                });
                
                p.btn.setAttribute('aria-expanded', 'true');
                p.panel.setAttribute('aria-hidden', 'false');
    
                if (p.onOpen) p.onOpen();
            });
    
            p.closeBtn.addEventListener('click', p.onClose);
        });
    
        document.addEventListener('click', (event) => {
            panels.forEach(p => {
                if (p.panel && p.panel.classList.contains('open') && !p.panel.contains(event.target)) {
                    p.onClose();
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                panels.forEach(p => p.onClose());
            }
        });
    }

    function setupCustomSelects() {
        document.querySelectorAll('.custom-select').forEach(select => {
            const trigger = select.querySelector('.custom-select-trigger');
            const options = select.nextElementSibling;
            const hiddenInputId = select.dataset.selectId;
            const hiddenInput = document.getElementById(hiddenInputId);
            select.addEventListener('click', (event) => {
                event.stopPropagation();
                const wasOpen = select.classList.contains('open');
                document.querySelectorAll('.custom-select').forEach(otherSelect => {
                    otherSelect.classList.remove('open');
                    otherSelect.nextElementSibling.classList.remove('open');
                });
                if (!wasOpen) {
                    select.classList.add('open');
                    options.classList.add('open');
                }
            });
            options.addEventListener('click', (event) => {
                if (event.target.classList.contains('custom-option')) {
                    trigger.textContent = event.target.textContent;
                    hiddenInput.value = event.target.dataset.value;
                    hiddenInput.dispatchEvent(new Event('change'));
                    select.classList.remove('open');
                    options.classList.remove('open');
                }
            });
        });
        window.addEventListener('click', () => {
            document.querySelectorAll('.custom-select').forEach(select => {
                select.classList.remove('open');
                select.nextElementSibling.classList.remove('open');
            });
        });
    }

    setupMobileNavigation();
    setupScrollEffects();
    setupPanelToggles();
    setupCustomSelects();
});