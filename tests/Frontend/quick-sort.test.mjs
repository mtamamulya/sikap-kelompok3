import test from 'node:test';
import assert from 'node:assert/strict';
import { quickSort } from '../../resources/js/quick-sort.js';

test('handles empty and single-item lists without mutating input', () => {
    const compare = (a, b) => a - b;
    assert.deepEqual(quickSort([], compare), []);
    const input = [7];
    assert.deepEqual(quickSort(input, compare), [7]);
    assert.notEqual(quickSort(input, compare), input);
});

test('sorts unsorted numbers in both directions and preserves all items', () => {
    const input = [4, 2, 9, 2, 0, -1];
    assert.deepEqual(quickSort(input, (a, b) => a - b), [-1, 0, 2, 2, 4, 9]);
    assert.deepEqual(quickSort(input, (a, b) => b - a), [9, 4, 2, 2, 0, -1]);
    assert.deepEqual(input, [4, 2, 9, 2, 0, -1]);
});

test('keeps records with matching names in original order', () => {
    const input = [{ id: 1, nama: 'Pelatihan' }, { id: 2, nama: 'Kuliah' }, { id: 3, nama: 'Pelatihan' }];
    const compare = new Intl.Collator('id', { sensitivity: 'base', numeric: true }).compare;
    assert.deepEqual(quickSort(input, (a, b) => compare(a.nama, b.nama)).map(row => row.id), [2, 1, 3]);
});

test('sorts ISO dates chronologically across different years', () => {
    const dates = ['2026-08-15', '2027-01-02', '2026-12-31', '2025-12-31'];
    const compare = (a, b) => a < b ? -1 : a > b ? 1 : 0;
    assert.deepEqual(quickSort(dates, compare), ['2025-12-31', '2026-08-15', '2026-12-31', '2027-01-02']);
    assert.deepEqual(quickSort(dates, (a, b) => -compare(a, b)), ['2027-01-02', '2026-12-31', '2026-08-15', '2025-12-31']);
});

test('handles an already ordered list and identical values', () => {
    assert.deepEqual(quickSort([1, 2, 3, 4], (a, b) => a - b), [1, 2, 3, 4]);
    assert.deepEqual(quickSort([3, 3, 3], (a, b) => a - b), [3, 3, 3]);
});
