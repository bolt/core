type StoredRecordValue = string | number | boolean | null | StoredRecordValue[] | { [key: string]: StoredRecordValue };

export function getRecords(type: string): StoredRecordValue | null {
    const records = localStorage.getItem(`records-${type}`);
    return records ? (JSON.parse(records) as StoredRecordValue) : null;
}
