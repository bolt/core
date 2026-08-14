export function getRecords(type: string) {
    // String() keeps the previous behaviour for a missing key: getItem returns
    // null, JSON.parse coerces it to "null" and yields null.
    // The local is typed so the inferred return is unknown rather than the any
    // that JSON.parse hands back.
    const records: unknown = JSON.parse(String(localStorage.getItem(`records-${type}`)));

    return records;
}
