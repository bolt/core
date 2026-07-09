type DebouncedFunction = (() => void) & {
    cancel: () => void;
};

export function debounce(callback: () => void, wait: number): DebouncedFunction {
    let timeout: ReturnType<typeof setTimeout> | null = null;

    const debounced = () => {
        if (timeout !== null) {
            clearTimeout(timeout);
        }

        timeout = setTimeout(() => {
            timeout = null;
            callback();
        }, wait);
    };

    debounced.cancel = () => {
        if (timeout !== null) {
            clearTimeout(timeout);
            timeout = null;
        }
    };

    return debounced;
}
