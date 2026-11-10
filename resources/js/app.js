import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
    initializeNavigation();
    initializeTables();
});

function initializeNavigation() {
    const shell = document.querySelector('[data-frontend-shell]');
    if (!shell) return;
    const state = Alpine.$data(shell);
    const key = shell.dataset.menuState;
    const navigation = shell.querySelector('[data-frontend-navigation]');
    const header = shell.querySelector('[data-frontend-header]');
    const workspace = header?.parentElement;
    const toggle = header?.querySelector('[aria-controls]');
    const mobile = matchMedia('(max-width: 1023px)');
    if (!navigation || !key) return;
    let previouslyOpen = false;
    Alpine.effect(() => {
        const menuTerbuka = state[key];            // selalu dibaca, supaya efek ini selalu ikut berubah
        const open = mobile.matches && menuTerbuka;
        navigation.inert = mobile.matches && !open;
        if (workspace) workspace.inert = open;
        if (open && !previouslyOpen) requestAnimationFrame(() => navigation.querySelector('a,button,summary')?.focus({ preventScroll: true }));
        if (!open && previouslyOpen) toggle?.focus({ preventScroll: true });
        previouslyOpen = open;
    });
    mobile.addEventListener('change', () => {
        state[key] = false;
        navigation.inert = mobile.matches;
        if (workspace) workspace.inert = false;
    });
    navigation.addEventListener('click', event => {
        if (event.target.closest('a')) state[key] = false;
    });
    document.addEventListener('keydown', event => {
        if (mobile.matches && state[key] && event.key === 'Tab') trapFocus(event, navigation);
        const dialog = [...document.querySelectorAll('[role=dialog][aria-modal=true],[role=alertdialog][aria-modal=true]')].find(el => el.getClientRects().length);
        if (dialog && event.key === 'Tab') trapFocus(event, dialog);
    });
}

function trapFocus(event, container) {
    const items = [...container.querySelectorAll('a[href],button,input,select,textarea,summary,[tabindex="0"]')].filter(el => !el.disabled && el.getClientRects().length && !el.closest('[inert]'));
    const first = items[0], last = items.at(-1);
    if (!first) return;
    if (event.shiftKey && (document.activeElement === first || !container.contains(document.activeElement))) {
        event.preventDefault(); last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault(); first.focus();
    }
}

function initializeTables() {
    const main = document.querySelector('.frontend-main');
    if (!main) return;
    const observer = new ResizeObserver(entries => entries.forEach(({target}) => {
        const scrolls = target.scrollWidth > target.clientWidth + 1;
        if (target.previousElementSibling?.matches('.table-scroll-hint')) target.previousElementSibling.hidden = !scrolls;
        target.tabIndex = scrolls ? 0 : -1;
    }));
    main.querySelectorAll('table').forEach(table => {
        let region = table.closest('.overflow-x-auto');
        if (!region) {
            region = document.createElement('div'); region.className = 'overflow-x-auto';
            table.before(region); region.append(table);
        }
        const hint = document.createElement('p'); hint.className = 'table-scroll-hint';
        hint.textContent = 'Geser tabel ke samping untuk melihat semua kolom.';
        hint.hidden = region.scrollWidth <= region.clientWidth + 1; region.before(hint);
        region.setAttribute('role', 'region'); region.setAttribute('aria-label', 'Tabel data, geser untuk melihat kolom lainnya');
        observer.observe(region);
    });
}

const liveSearchTimers = new WeakMap();

document.addEventListener('input', (event) => {
    const input = event.target;

    if (!(input instanceof HTMLInputElement) || !input.matches('[data-live-search]')) {
        return;
    }

    const form = input.form;
    if (!form) {
        return;
    }

    window.clearTimeout(liveSearchTimers.get(input));
    liveSearchTimers.set(input, window.setTimeout(() => form.requestSubmit(), 400));
});
