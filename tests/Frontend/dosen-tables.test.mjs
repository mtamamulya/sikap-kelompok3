import test from 'node:test';
import assert from 'node:assert/strict';
import { selectRows, sortValue } from '../../resources/js/dosen-tables.js';

const rows = Array.from({ length: 27 }, (_, id) => ({ id, text: `Penelitian ${id} ${id % 2 ? 'Mandiri' : 'Hibah'}`, values: [`Penelitian ${id}`, String(id), id % 2 ? 'Mandiri' : 'Hibah'] }));

test('search and column filters apply before pagination and sorting', () => {
    const result = selectRows(rows, { search: 'penelitian', filters: [{ column: 2, value: 'Hibah' }], column: 1, direction: -1, size: 5, page: 2 });
    assert.equal(result.total, 14);
    assert.equal(result.pages, 3);
    assert.deepEqual(result.rows.map(row => row.id), [16, 14, 12, 10, 8]);
    assert.equal(rows[0].id, 0);
});

test('empty searches, out of range pages, and page sizes are consistent', () => {
    const empty = selectRows(rows, { search: 'tidak ada' });
    assert.equal(empty.total, 0);
    assert.equal(empty.page, 1);
    assert.deepEqual(empty.rows, []);
    const last = selectRows(rows, { size: 10, page: 99 });
    assert.equal(last.page, 3);
    assert.equal(last.rows.length, 7);
    assert.equal(selectRows(rows, { size: 25 }).rows.length, 25);
});

test('dates in Indonesian sort chronologically and academic years remain text', () => {
    assert.ok(sortValue('2 Januari 2027') > sortValue('31 Desember 2026'));
    assert.equal(sortValue('2025/2026'), '2025/2026');
    assert.equal(sortValue('10'), 10);
    const dates = ['15 Agustus 2026', '3 Oktober 2026', '26 Juli 2026'].map(text => ({ text, values: [text] }));
    assert.deepEqual(selectRows(dates, { column: 0 }).rows.map(row => row.text), ['26 Juli 2026', '15 Agustus 2026', '3 Oktober 2026']);
});

test('duplicate values retain original row IDs and action targets', () => {
    const same = [{ id: 3, text: 'A', values: ['A'] }, { id: 1, text: 'A', values: ['A'] }];
    assert.deepEqual(selectRows(same, { column: 0 }).rows.map(row => row.id), [3, 1]);
});
