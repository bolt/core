export function getRecords(type: string) {
    // String() keeps the previous behaviour for a missing key: getItem returns
    // null, JSON.parse coerces it to "null" and yields null.
    return JSON.parse(String(localStorage.getItem(`records-${type}`)));
}
