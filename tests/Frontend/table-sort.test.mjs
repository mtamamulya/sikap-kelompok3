import test from 'node:test';
import assert from 'node:assert/strict';
import { initializeTableSort } from '../../resources/js/table-sort.js';

function element(text = '') {
    return {
        textContent: text, dataset: {}, attributes: {}, children: [], listeners: {},
        setAttribute(key, value) { this.attributes[key] = value; },
        append(...children) { this.children.push(...children); },
        replaceChildren(...children) { this.children = children; },
        addEventListener(event, handler) { this.listeners[event] = handler; },
    };
}

test('column clicks reorder rows, toggle direction, and keep each row and action together', () => {
    const previousDocument = globalThis.document;
    const headers = ['Judul', 'Tahun Pelaksanaan', 'Aksi'].map(element);
    const makeRow = (name, year, link) => ({ cells: [element(name), element(year), element(link)] });
    const initialRows = [makeRow('Zebra', '10', '/detail/1'), makeRow('apel', '2', '/detail/2'), makeRow('apel', '9', '/detail/3')];
    const body = { rows: [...initialRows], append(...rows) { this.rows = rows; } };
    let status;
    const table = {
        dataset: {}, tBodies: [body], tHead: { rows: [{ cells: headers }] },
        parentElement: { before(value) { status = value; } },
    };
    globalThis.document = { createElement: () => element(), createTextNode: text => element(text) };
    try {
        const root = { querySelectorAll: () => [table] };
        initializeTableSort(root);
        const titleButton = headers[0].children[0];
        const yearButton = headers[1].children[0];
        assert.equal(headers[2].children.length, 0, 'Aksi must not become sortable');

        titleButton.listeners.click();
        assert.deepEqual(body.rows.map(row => row.cells[2].textContent), ['/detail/2', '/detail/3', '/detail/1']);
        assert.equal(headers[0].attributes['aria-sort'], 'ascending');
        titleButton.listeners.click();
        assert.deepEqual(body.rows.map(row => row.cells[0].textContent), ['Zebra', 'apel', 'apel']);
        assert.equal(headers[0].attributes['aria-sort'], 'descending');

        yearButton.listeners.click();
        assert.deepEqual(body.rows.map(row => row.cells[1].textContent), ['2', '9', '10']);
        assert.equal(headers[0].attributes['aria-sort'], 'none');
        assert.equal(headers[1].attributes['aria-sort'], 'ascending');
        assert.match(status.textContent, /Tahun Pelaksanaan, terkecil ke terbesar/);
        yearButton.listeners.click();
        assert.deepEqual(body.rows.map(row => row.cells[1].textContent), ['10', '9', '2']);

        initializeTableSort(root);
        assert.equal(headers[0].children[0], titleButton, 'initializing twice must not duplicate controls');
        assert.deepEqual(initialRows.map(row => row.cells[0].textContent), ['Zebra', 'apel', 'apel']);
    } finally {
        globalThis.document = previousDocument;
    }
});
