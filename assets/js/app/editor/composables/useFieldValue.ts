import { ref, type Ref } from 'vue';

type FieldValue = string | number | boolean | null;
type WidenFieldValue<T extends FieldValue> = T extends string
    ? string
    : T extends number
      ? number
      : T extends boolean
        ? boolean
        : T;

/**
 * Composition-API replacement for the `mixins/value` field mixin:
 * `val` holds the editable field value, `rawVal` its HTML-unescaped form.
 * Both are writable refs initialized from the `value` prop, matching the
 * mixin's `data` + `mounted` behavior.
 */
export function useFieldValue<T extends FieldValue>(
    value: T,
): {
    val: Ref<WidenFieldValue<T>>;
    rawVal: Ref<string>;
};
export function useFieldValue<T extends FieldValue = string>(
    value: T | undefined,
): {
    val: Ref<WidenFieldValue<T> | undefined>;
    rawVal: Ref<string>;
};
export function useFieldValue<T extends FieldValue = string>(value: T | undefined) {
    const val: Ref<WidenFieldValue<T> | undefined> = ref(value) as Ref<WidenFieldValue<T> | undefined>;

    // Make sure the "rawVal" is 'unescaped'
    const node = document.createElement('textarea');
    node.innerHTML = String(value ?? '');
    const rawVal = ref(node.value);

    return { val, rawVal };
}
