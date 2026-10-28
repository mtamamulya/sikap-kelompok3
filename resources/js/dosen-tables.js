import { quickSort } from './quick-sort.js';

const months = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];

export function sortValue(value) {
    const text = String(value).trim();
    const date = text.toLowerCase().match(/^(\d{1,2})\s+([a-z]+)\s+(\d{4})$/);
    if (date && months.includes(date[2])) return Number(date[3]) * 10000 + (months.indexOf(date[2]) + 1) * 100 + Number(date[1]);
    if (/^-?\d+(\.\d+)?$/.test(text)) return Number(text);
    return text;
}

export function selectRows(rows, { search = '', filters = [], column = -1, direction = 1, size = 10, page = 1 } = {}) {
    const collator = new Intl.Collator('id', { sensitivity: 'base', numeric: true });
    let result = rows.filter(row => row.text.toLocaleLowerCase('id').includes(search.toLocaleLowerCase('id'))
        && filters.every(filter => !filter.value || row.values[filter.column]?.toLowerCase() === filter.value.toLowerCase()));
    if (column >= 0) result = quickSort(result, (a, b) => {
        const left = sortValue(a.values[column]);
        const right = sortValue(b.values[column]);
        return direction * (typeof left === 'number' && typeof right === 'number' ? left - right : collator.compare(String(left), String(right)));
    });
    const total = result.length;
    const pages = Math.max(1, Math.ceil(total / size));
    page = Math.min(Math.max(1, page), pages);
    return { rows: result.slice((page - 1) * size, page * size), total, pages, page };
}

export function initializeDosenTables() {
    if (!document.body.hasAttribute('data-dosen-tables')) return;
    document.querySelectorAll('main table').forEach((table, tableIndex) => {
        // Editable member rows are managed by the form's Alpine component.
        if (table.closest('form') || table.querySelector('template') || !table.tHead || !table.tBodies[0]) return;
        const body = table.tBodies[0];
        const headers = Array.from(table.tHead.rows[0].cells);
        const rows = Array.from(body.rows).filter(row => row.cells.length === headers.length).map(element => ({
            element, text: element.textContent.trim(), values: Array.from(element.cells, cell => cell.dataset.sortValue ?? cell.textContent.trim()),
        }));
        const panel = table.closest('section') ?? table.parentElement.parentElement;
        const wrapper = table.parentElement;
        const state = { search: '', filters: [], column: -1, direction: 1, size: 10, page: 1 };
        const controls = document.createElement('div');
        controls.className = 'dt-toolbar';
        controls.innerHTML = `<label>Tampilkan <select aria-label="Jumlah baris"><option>5</option><option selected>10</option><option>25</option><option>50</option></select> baris</label><label>Cari: <input type="search" aria-label="Cari dalam tabel" placeholder="Cari data..."></label>`;
        wrapper.before(controls);
        const footer = document.createElement('div');
        footer.className = 'dt-footer';
        const info = document.createElement('span');
        info.setAttribute('role', 'status');
        info.setAttribute('aria-live', 'polite');
        const pagination = document.createElement('nav');
        pagination.setAttribute('aria-label', `Halaman tabel ${tableIndex + 1}`);
        footer.append(info, pagination);
        wrapper.after(footer);
        panel.querySelectorAll('nav[aria-label="Halaman"]').forEach(nav => nav.hidden = true);
        const empty = document.createElement('p');
        empty.className = 'dt-empty';
        empty.textContent = 'Tidak ada data yang cocok.';
        wrapper.after(empty);
        table.classList.add('dt-table');
        const buttons = [];

        headers.forEach((header, column) => {
            const label = header.textContent.replace(/[↑↓↕▲▼]/g, '').trim();
            if (/aksi/i.test(label)) return;
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'dt-sort';
            const name = document.createElement('span');
            name.textContent = label;
            const arrows = document.createElement('span');
            arrows.className = 'dt-arrows';
            arrows.setAttribute('aria-hidden', 'true');
            arrows.innerHTML = '<span>▲</span><span>▼</span>';
            button.append(name, arrows);
            header.replaceChildren(button);
            header.setAttribute('aria-sort', 'none');
            buttons.push({ header, column });
            button.addEventListener('click', () => {
                state.direction = state.column === column ? -state.direction : 1;
                state.column = column;
                state.page = 1;
                render();
            });
        });

        // Connect the existing page filters to the same rows and pagination.
        const form = panel.querySelector('form[method="GET"]');
        if (form) {
            form.addEventListener('submit', event => event.preventDefault());
            const oldSearch = form.querySelector('input[type="search"]');
            if (oldSearch) oldSearch.closest('.relative').hidden = true;
            form.querySelectorAll('select').forEach(select => {
                const labels = { tahun: 'Tahun', dana: 'Sumber Dana', kelas: 'Kelas', semester: 'Semester', tingkat: 'Tingkat', status: 'Status' };
                const column = headers.findIndex(header => header.textContent.includes(labels[select.name] ?? select.name));
                if (column < 0) return;
                select.addEventListener('change', () => {
                    const existing = state.filters.find(filter => filter.column === column);
                    const value = select.value;
                    if (existing) existing.value = value;
                    else state.filters.push({ column, value });
                    state.page = 1;
                    render();
                });
            });
        }

        function render() {
            const result = selectRows(rows, state);
            state.page = result.page;
            const visible = new Set(result.rows);
            // Move whole rows so each action continues to target its original record.
            body.append(...result.rows.map(row => row.element));
            rows.forEach(row => row.element.hidden = !visible.has(row));
            empty.hidden = result.total > 0;
            const start = result.total ? (result.page - 1) * state.size + 1 : 0;
            const end = Math.min(result.page * state.size, result.total);
            info.textContent = `Menampilkan ${start}–${end} dari ${result.total} data${result.total !== rows.length ? ` (disaring dari ${rows.length} data)` : ''}`;
            pagination.replaceChildren();
            const addButton = (label, target, disabled, current = false) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.disabled = disabled;
                if (current) button.setAttribute('aria-current', 'page');
                button.addEventListener('click', () => { state.page = target; render(); });
                pagination.append(button);
            };
            addButton('«', 1, result.page === 1);
            addButton('‹', result.page - 1, result.page === 1);
            for (let page = Math.max(1, result.page - 2); page <= Math.min(result.pages, result.page + 2); page++) addButton(String(page), page, false, page === result.page);
            addButton('›', result.page + 1, result.page === result.pages);
            addButton('»', result.pages, result.page === result.pages);
            buttons.forEach(({ header, column }) => header.setAttribute('aria-sort', state.column === column ? (state.direction === 1 ? 'ascending' : 'descending') : 'none'));
        }
        controls.querySelector('input').addEventListener('input', event => { state.search = event.target.value; state.page = 1; render(); });
        controls.querySelector('select').addEventListener('change', event => { state.size = Number(event.target.value); state.page = 1; render(); });
        render();
    });
}
