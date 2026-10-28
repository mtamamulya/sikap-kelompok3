import { quickSort } from './quick-sort';

export function initializeDashboardSort() {
    const table = document.querySelector('[data-dashboard-sort]');
    if (!table) return;

    const body = table.tBodies[0];
    const rows = Array.from(body.rows);
    const buttons = Array.from(table.querySelectorAll('[data-sort-key]'));
    const status = document.getElementById('urutan-kegiatan');
    const collator = new Intl.Collator('id', { sensitivity: 'base', numeric: true });
    let activeKey = 'tanggal';
    let direction = 'descending';

    function renderOrder() {
        const factor = direction === 'ascending' ? 1 : -1;
        const sorted = quickSort(rows, (left, right) => {
            const a = left.dataset[activeKey];
            const b = right.dataset[activeKey];
            const order = activeKey === 'tanggal'
                ? (a < b ? -1 : a > b ? 1 : 0)
                : collator.compare(a, b);
            return factor * order;
        });
        body.append(...sorted);

        for (const button of buttons) {
            const active = button.dataset.sortKey === activeKey;
            button.closest('th').setAttribute('aria-sort', active ? direction : 'none');
            button.querySelector('[data-sort-icon]').textContent = active
                ? (direction === 'ascending' ? '↑' : '↓') : '↕';
        }

        const current = buttons.find(button => button.dataset.sortKey === activeKey);
        const orderLabel = activeKey === 'tanggal'
            ? (direction === 'ascending' ? 'terlama lebih dulu' : 'terbaru lebih dulu')
            : (direction === 'ascending' ? 'A–Z' : 'Z–A');
        status.textContent = `Urutan: ${current.dataset.sortLabel}, ${orderLabel}.`;
    }

    for (const button of buttons) {
        button.addEventListener('click', () => {
            const key = button.dataset.sortKey;
            direction = activeKey === key
                ? (direction === 'ascending' ? 'descending' : 'ascending')
                : (key === 'tanggal' ? 'descending' : 'ascending');
            activeKey = key;
            renderOrder();
        });
    }

    renderOrder();
}
