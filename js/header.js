document.addEventListener('DOMContentLoaded', function () {

    const header = document.querySelector('header');
    const mainContainer = document.querySelector('.main-container');
    const menuIcon = document.querySelector('.fa-bars');
    const navBar = document.querySelector('.nav-bar');
    const searchToggleBtn = document.getElementById('search-toggle-btn');
    const filterControls = document.getElementById('filter-controls');
    const closeFilterBtn = document.getElementById('close-filter-btn');

    function setupMobileNavigation() {
        if (!menuIcon || !navBar) return;

        const closeMenu = () => {
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
                closeMenu();
            }
        });

        window.addEventListener('scroll', closeMenu);
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

    function setupSearchToggle() {
        if (searchToggleBtn && filterControls && closeFilterBtn && mainContainer) {
            
            const INITIAL_PADDING_TOP = 140;

            searchToggleBtn.addEventListener('click', () => {
                const filterHeight = filterControls.offsetHeight;
                mainContainer.style.paddingTop = `${INITIAL_PADDING_TOP + filterHeight}px`;

                filterControls.classList.add('open');
                searchToggleBtn.classList.add('hidden');
                searchToggleBtn.setAttribute('aria-expanded', 'true');
                filterControls.setAttribute('aria-hidden', 'false');
            });

            closeFilterBtn.addEventListener('click', () => {
                mainContainer.style.paddingTop = `${INITIAL_PADDING_TOP}px`;

                filterControls.classList.remove('open');
                searchToggleBtn.classList.remove('hidden');
                searchToggleBtn.setAttribute('aria-expanded', 'false');
                filterControls.setAttribute('aria-hidden', 'true');
            });
        }
    }

    function setupCustomSelects() {
        document.querySelectorAll('.custom-select').forEach(select => {
            const trigger = select.querySelector('.custom-select-trigger');
            const options = select.nextElementSibling;
            const hiddenInputId = select.dataset.selectId;
            const hiddenInput = document.getElementById(hiddenInputId);

            select.addEventListener('click', (event) => {
                event.stopPropagation();
                
                document.querySelectorAll('.custom-select').forEach(otherSelect => {
                    if (otherSelect !== select) {
                        otherSelect.classList.remove('open');
                        otherSelect.nextElementSibling.classList.remove('open');
                    }
                });
                
                select.classList.toggle('open');
                options.classList.toggle('open');
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
    setupSearchToggle();
    setupCustomSelects();
});