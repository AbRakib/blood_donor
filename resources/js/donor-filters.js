export function initializeDonorFilters() {
    const filters = [];

    function closeFilters(except = null) {
        filters.forEach(({trigger, menu}) => {
            if (menu === except) return;
            menu.hidden = true;
            trigger.setAttribute('aria-expanded', 'false');
        });
    }

    document.querySelectorAll('#directory .dirtools select').forEach(select => {
        const wrapper = document.createElement('div');
        wrapper.className = 'donor-filter';
        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'donor-filter-trigger';
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');
        const label = document.createElement('span');
        const chevron = document.createElement('i');
        chevron.className = 'fi fi-rr-angle-small-down';
        chevron.setAttribute('aria-hidden', 'true');
        trigger.append(label, chevron);
        const menu = document.createElement('div');
        menu.id = `${select.id}-options`;
        menu.className = 'donor-filter-menu';
        menu.hidden = true;
        menu.setAttribute('role', 'listbox');
        menu.setAttribute('aria-label', select.options[0].textContent);
        trigger.setAttribute('aria-controls', menu.id);
        select.before(wrapper);
        wrapper.append(select, trigger, menu);
        select.hidden = true;
        filters.push({trigger, menu});

        function synchronizeOptions() {
            label.textContent = select.selectedOptions[0]?.textContent || select.options[0].textContent;
            trigger.classList.toggle('is-selected', select.value !== '');
            menu.replaceChildren(...Array.from(select.options, option => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'donor-filter-option';
                button.setAttribute('role', 'option');
                button.setAttribute('aria-selected', String(option.selected));
                button.textContent = option.textContent;
                button.disabled = option.disabled;
                button.tabIndex = -1;
                button.addEventListener('click', () => {
                    select.value = option.value;
                    synchronizeOptions();
                    closeFilters();
                    trigger.focus();
                    select.dispatchEvent(new Event('change', {bubbles: true}));
                });
                return button;
            }));
        }

        function openFilter(last = false) {
            closeFilters(menu);
            menu.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
            const options = menu.querySelectorAll('button:not(:disabled)');
            const selected = menu.querySelector('[aria-selected="true"]:not(:disabled)');
            (last ? options[options.length - 1] : selected || options[0])?.focus();
        }

        trigger.addEventListener('click', () => {
            if (menu.hidden) openFilter();
            else closeFilters();
        });
        trigger.addEventListener('keydown', event => {
            if (!['ArrowDown', 'ArrowUp'].includes(event.key)) return;
            event.preventDefault();
            openFilter(event.key === 'ArrowUp');
        });
        wrapper.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                closeFilters();
                trigger.focus();
            }
            if (menu.hidden || !['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            const options = Array.from(menu.querySelectorAll('button:not(:disabled)'));
            const index = options.indexOf(document.activeElement);
            const next = event.key === 'Home' ? 0 : event.key === 'End' ? options.length - 1
                : (index + (event.key === 'ArrowDown' ? 1 : -1) + options.length) % options.length;
            options[next]?.focus();
        });
        wrapper.addEventListener('focusout', event => {
            if (!wrapper.contains(event.relatedTarget)) closeFilters();
        });
        select.addEventListener('change', synchronizeOptions);
        new MutationObserver(synchronizeOptions).observe(select, {childList: true, subtree: true});
        synchronizeOptions();
    });

    document.addEventListener('click', event => {
        if (!event.target.closest('.donor-filter')) closeFilters();
    });
}
