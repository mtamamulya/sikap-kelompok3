import './bootstrap';

const liveSearchTimers = new WeakMap();

document.addEventListener('input', (event) => {
    const input = event.target;

    if (!(input instanceof HTMLInputElement)) {
        return;
    }

    if (input.matches('[data-table-live-search]')) {
        const section = input.closest('section');

        if (!section) {
            return;
        }

        section.querySelectorAll('tbody').forEach((tbody) => {
            const rows = [...tbody.querySelectorAll('tr:not([data-live-search-empty])')];
            const query = input.value.trim().toLocaleLowerCase();
            let visibleRows = 0;

            rows.forEach((row) => {
                row.hidden = !row.textContent.toLocaleLowerCase().includes(query);
                visibleRows += Number(!row.hidden);
            });

            let emptyRow = tbody.querySelector('[data-live-search-empty]');

            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.dataset.liveSearchEmpty = '';
                const cell = document.createElement('td');
                cell.className = 'px-4 py-8 text-center text-gray-500';
                cell.colSpan = rows[0]?.children.length || 1;
                cell.textContent = 'Tidak ada hasil pencarian.';
                emptyRow.append(cell);
                tbody.append(emptyRow);
            }

            emptyRow.hidden = visibleRows > 0;
        });

        return;
    }

    if (!input.matches('[data-live-search]')) {
        return;
    }

    const form = input.form;
    if (!form) {
        return;
    }

    window.clearTimeout(liveSearchTimers.get(input));
    liveSearchTimers.set(input, window.setTimeout(() => form.requestSubmit(), 250));
});

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (form instanceof HTMLFormElement && form.querySelector('[data-table-live-search]')) {
        event.preventDefault();
    }
});
