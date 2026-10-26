/**
 * Twig passes `false` for an unset field option, such as a pattern or an error
 * message. Vue 2 drops a non-boolean attribute bound to `false`, but Vue 3
 * renders it as "false"; `undefined` drops it in both. Every other value is
 * returned as it is.
 *
 * @see https://v3-migration.vuejs.org/breaking-changes/attribute-coercion.html
 */
export function optionalAttribute<T>(value: T | false): T | undefined {
    return value === false ? undefined : value;
}
