import { quickSort } from './quick-sort.js';

export function initializeTableSort(root = document) {
    const collator = new Intl.Collator('id', { sensitivity: 'base', numeric: true });

    for (const table of root.querySelectorAll('[data-quick-sort]')) {
        if (table.dataset.sortReady) continue;
        table.dataset.sortReady = 'true';
        const body = table.tBodies[0];
        const rows = Array.from(body.rows);
        const controls = [];
        let activeColumn = -1;
        let direction = 'ascending';

        const status = document.createElement('p');
        status.className = 'mb-3 text-sm text-gray-600';
        status.setAttribute('role', 'status');
        status.setAttribute('aria-live', 'polite');
        status.textContent = 'Klik judul kolom untuk mengurutkan data.';
        table.parentElement.before(status);

        Array.from(table.tHead.rows[0].cells).forEach((header, column) => {
            const label = header.textContent.trim();
            if (label === 'Aksi' || header.dataset.sortable === 'false') return;
            const numeric = /tahun|anggota|jumlah|semester/i.test(label);
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'flex w-full cursor-pointer items-center gap-2 rounded text-left hover:text-brand focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand';
            button.append(document.createTextNode(label + ' '));
            const icon = document.createElement('span');
            icon.setAttribute('aria-hidden', 'true');
            icon.textContent = '↕';
            button.append(icon);
            header.replaceChildren(button);
            header.setAttribute('aria-sort', 'none');
            controls.push({ header, icon, column });

            button.addEventListener('click', () => {
                direction = activeColumn === column
                    ? (direction === 'ascending' ? 'descending' : 'ascending')
                    : 'ascending';
                activeColumn = column;
                const factor = direction === 'ascending' ? 1 : -1;
                const read = row => row.cells[column].dataset.sortValue ?? row.cells[column].textContent.trim();
                const sorted = quickSort(rows, (left, right) => {
                    const a = read(left);
                    const b = read(right);
                    const order = numeric && a !== '' && b !== '' && Number.isFinite(Number(a)) && Number.isFinite(Number(b))
                        ? Number(a) - Number(b) : collator.compare(a, b);
                    return factor * order;
                });
                body.append(...sorted);

                for (const control of controls) {
                    const active = control.column === activeColumn;
                    control.header.setAttribute('aria-sort', active ? direction : 'none');
                    control.icon.textContent = active ? (direction === 'ascending' ? '↑' : '↓') : '↕';
                }
                const orderLabel = numeric
                    ? (direction === 'ascending' ? 'terkecil ke terbesar' : 'terbesar ke terkecil')
                    : (direction === 'ascending' ? 'A–Z' : 'Z–A');
                status.textContent = `Urutan: ${label}, ${orderLabel}.`;
            });
        });
    }
}
