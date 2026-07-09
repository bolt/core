export type SelectOption = {
    key: string | number;
    value: string;
    selected?: boolean;
    link_to_record_url?: string;
};

export type SelectValue = string | number | Array<string | number>;
export type SelectedValue = SelectOption | SelectOption[] | null;

/**
 * Emitted by File/Image list items when a field is moved or removed. The
 * `fieldName` carries the (possibly nested) index the parent list uses to
 * locate the item.
 */
export type FieldMovePayload = {
    fieldName: string;
};
