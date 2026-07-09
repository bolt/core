<template>
    <div ref="rootEl">
        <div class="input-group">
            <flat-pickr
                v-model="val"
                class="form-control editor--date"
                :config="config"
                :disabled="readonly"
                :form="form"
                :name="name"
                placeholder="Select date"
                :data-errormessage="errormessage === false ? undefined : errormessage"
            >
            </flat-pickr>
            <button
                class="btn btn-tertiary"
                :class="{ 'btn-outline-secondary': readonly }"
                type="button"
                :disabled="readonly"
                data-toggle
                aria-label="Date picker"
                onclick="this.blur()"
            >
                <i class="fa fa-calendar">
                    <span class="sr-only" aria-hidden="true">{{ parsedLabels.toggle }}</span>
                </i>
            </button>
            <button
                class="btn btn-tertiary"
                :class="{ 'btn-outline-secondary': readonly }"
                type="button"
                :disabled="readonly"
                data-clear
                aria-label="Reset date"
                onclick="this.blur()"
            >
                <i class="fa fa-times">
                    <span class="sr-only" aria-hidden="true">{{ parsedLabels.clear }}</span>
                </i>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUpdated } from 'vue';
import type { Locale } from 'flatpickr/dist/types/locale';
import type { Options } from 'flatpickr/dist/types/options';
import flatPickr from 'vue-flatpickr-component';
import { useFieldValue } from '../composables/useFieldValue';

declare const require: (path: string) => { default: Record<string, Locale> };

const props = defineProps<{
    value?: string;
    name: string;
    readonly: boolean;
    mode?: string;
    form: string;
    locale?: string;
    labels?: string | Record<string, string>;
    required: boolean;
    errormessage: string | boolean;
}>();

const mode = computed(() => props.mode || 'date');
const locale = computed(() => props.locale || 'en');
const labels = computed(() => props.labels || '');

const { val } = useFieldValue<string>(props.value ?? '');

const rootEl = ref<HTMLElement | null>(null);

function getLocale() {
    if (locale.value !== 'en') {
        try {
            return require(`flatpickr/dist/l10n/${locale.value}.js`).default[locale.value];
        } catch {
            return undefined;
        }
    }
    return undefined;
}

// flatpickr rejects `locale: undefined` (throws "invalid locale undefined"), so
// only include the key when a locale was actually resolved; otherwise flatpickr
// falls back to its built-in English default.
const flatpickrLocale = getLocale();
const config = ref<Partial<Options>>({
    wrap: true,
    altFormat: mode.value === 'datetime' ? 'F j, Y - h:i K' : 'F j, Y',
    altInput: true,
    dateFormat: 'Y-m-d H:i:S',
    enableTime: mode.value === 'datetime',
    ...(flatpickrLocale ? { locale: flatpickrLocale } : {}),
});

const parsedLabels = computed(() => {
    try {
        return typeof labels.value === 'string' ? JSON.parse(labels.value || '{}') : labels.value;
    } catch {
        return {};
    }
});

function fixRequired(reportValidity = false) {
    if (!props.required || !rootEl.value) {
        return;
    }

    const input = rootEl.value.querySelector('.editor--date.input') as HTMLInputElement;
    if (!input) return;

    if (val.value === '') {
        input.required = true;
    } else {
        input.removeAttribute('required');
    }

    // Only surface the native validation popup after a user interaction or a
    // subsequent update — never on the initial mount. Calling reportValidity()
    // on mount would flash "please fill in this field" on page load for every
    // empty required date field, before the user has touched the form.
    if (!reportValidity) {
        return;
    }

    if (input.reportValidity) {
        input.reportValidity();
    }
    if (input.setCustomValidity) {
        input.setCustomValidity('');
    }
}

onMounted(() => fixRequired(false));
onUpdated(() => fixRequired(true));

watch(val, () => {
    fixRequired(true);
});
</script>
