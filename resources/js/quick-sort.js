/** Three-way Quick Sort; equal values retain their original order. */
export function quickSort(items, compare) {
    if (items.length < 2) return [...items];

    const pivot = items[Math.floor(items.length / 2)];
    const before = [];
    const equal = [];
    const after = [];

    for (const item of items) {
        const order = compare(item, pivot);
        if (order < 0) before.push(item);
        else if (order > 0) after.push(item);
        else equal.push(item);
    }

    return [...quickSort(before, compare), ...equal, ...quickSort(after, compare)];
}
