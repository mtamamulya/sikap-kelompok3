import './bootstrap';
import { initializeDosenTables } from './dosen-tables';

function initializeSorting() {
    initializeDosenTables();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSorting, { once: true });
} else {
    initializeSorting();
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
