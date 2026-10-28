export function enhanceSearchSelect(select) {
    const field = select.closest('.search-field');
    const label = field.querySelector('.field-label');
    const container = document.createElement('div');
    container.className = field.className;
    container.append(...field.childNodes);
    field.replaceWith(container);
    label.id = `${select.id}-label`;

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'search-select-trigger';
    trigger.setAttribute('role', 'combobox');
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');
    trigger.setAttribute('aria-labelledby', `${label.id} ${select.id}-value`);
    trigger.setAttribute('aria-controls', `${select.id}-options`);
    const value = document.createElement('span');
    value.id = `${select.id}-value`;
    trigger.append(value);
    const list = document.createElement('div');
    list.id = `${select.id}-options`;
    list.className = 'search-select-options';
    list.setAttribute('role', 'listbox');
    list.setAttribute('aria-labelledby', label.id);
    list.hidden = true;
    select.hidden = true;
    select.after(trigger);
    container.append(list);
    let activeIndex = 0;
    let typed = '';
    let typeTimer;

    function highlight(index) {
        activeIndex = index;
        [...list.children].forEach((option, position) => option.classList.toggle('is-active', position === index));
        const active = list.children[index];
        if (active) {
            trigger.setAttribute('aria-activedescendant', active.id);
            active.scrollIntoView({block: 'nearest'});
        }
    }

    function close() {
        list.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        trigger.removeAttribute('aria-activedescendant');
        container.classList.remove('is-open');
    }

    function sync() {
        value.textContent = select.selectedOptions[0]?.textContent || '';
        list.replaceChildren(...[...select.options].map((option, index) => {
            const item = document.createElement('div');
            item.id = `${select.id}-option-${index}`;
            item.setAttribute('role', 'option');
            item.setAttribute('aria-selected', String(option.selected));
            item.textContent = option.textContent;
            item.addEventListener('mousedown', event => event.preventDefault());
            item.addEventListener('click', () => choose(index));
            return item;
        }));
    }

    function choose(index) {
        select.selectedIndex = index;
        select.dispatchEvent(new Event('change', {bubbles: true}));
        close();
        trigger.focus();
    }

    function open() {
        list.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        container.classList.add('is-open');
        highlight(Math.max(select.selectedIndex, 0));
    }

    trigger.addEventListener('click', () => list.hidden ? open() : close());
    trigger.addEventListener('blur', close);
    trigger.addEventListener('keydown', event => {
        if (event.key === 'Tab') { close(); return; }
        if (event.key === 'Escape') { event.preventDefault(); close(); return; }
        if (['ArrowDown', 'ArrowUp', 'Home', 'End', 'Enter', ' '].includes(event.key)) {
            event.preventDefault();
            if (list.hidden) { open(); return; }
            if (event.key === 'Enter' || event.key === ' ') { choose(activeIndex); return; }
            highlight(event.key === 'Home' ? 0 : event.key === 'End' ? select.options.length - 1 : Math.max(0, Math.min(select.options.length - 1, activeIndex + (event.key === 'ArrowDown' ? 1 : -1))));
        } else if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
            event.preventDefault();
            if (list.hidden) open();
            clearTimeout(typeTimer);
            typed += event.key.toLocaleLowerCase();
            const index = [...select.options].findIndex(option => option.textContent.toLocaleLowerCase().startsWith(typed));
            if (index >= 0) highlight(index);
            typeTimer = setTimeout(() => { typed = ''; }, 600);
        }
    });
    document.addEventListener('click', event => { if (!container.contains(event.target)) close(); });
    select.addEventListener('change', sync);
    new MutationObserver(sync).observe(select, {childList: true});
    sync();
}
