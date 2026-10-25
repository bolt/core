<template>
    <div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">{{ prefix }}</span>
            </div>
            <input
                v-model="val"
                class="form-control"
                :name="name"
                :aria-label="name"
                placeholder="…"
                type="text"
                :class="fieldClass"
                :readonly="readonly || !edit"
                :required="required"
                :data-errormessage="typeof errormessage === 'string' ? errormessage : undefined"
                :pattern="typeof pattern === 'string' ? pattern : undefined"
                :title="name"
            />
            <div class="input-group-append">
                <button
                    type="button"
                    class="btn btn-tertiary dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    :disabled="readonly"
                    :class="[{ 'btn-primary': edit }, { 'btn-secondary': !edit }]"
                >
                    <i class="fas fa-fw" :class="`fa-${icon}`"></i>
                    {{ buttonText }}
                </button>
                <div class="dropdown-menu">
                    <template v-if="!edit">
                        <button class="dropdown-item" type="button" @click="editSlug">
                            <i class="fas fa-pencil-alt fa-fw"></i>
                            {{ labels.button_edit }}
                        </button>
                    </template>
                    <template v-if="!locked">
                        <button class="dropdown-item" type="button" @click="lockSlug">
                            <i class="fas fa-lock fa-fw"></i>
                            {{ labels.button_locked }}
                        </button>
                    </template>
                    <button class="dropdown-item" type="button" @click="generateSlug">
                        <i class="fas fa-link fa-fw"></i>
                        {{ labels.generate_from }}
                        {{ generate }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { eventBus } from '../../eventBus';
import { slugify } from '../../../filters/string';
import { useFieldValue } from '../composables/useFieldValue';

const props = defineProps<{
    value?: string;
    name?: string;
    prefix?: string;
    fieldClass?: string;
    generate: string;
    labels: Record<string, string>;
    required?: boolean;
    readonly?: boolean;
    errormessage?: string | boolean;
    pattern?: string | boolean;
    localize?: boolean;
    isNew?: boolean;
}>();

const { val } = useFieldValue(props.value);

const edit = ref(false);
const locked = ref(true);
const buttonText = ref(props.labels.button_locked);
const icon = ref('lock');
const generateSourceFields = computed(() => parseGenerateSourceFields());

onMounted(() => {
    setTimeout(() => {
        const title = getGeneratedSourceValue();
        if (shouldGenerateFromTitle(title)) {
            icon.value = 'unlock';
            buttonText.value = props.labels.button_unlocked;
            emitGenerateFromTitle(true);
            generateSlug();
        }
    }, 0);

    eventBus.on('slugify-from-title', generateSlugFromSource);
});

onBeforeUnmount(() => {
    eventBus.off('slugify-from-title', generateSlugFromSource);
});

function shouldGenerateFromTitle(title: string) {
    return props.isNew ? true : title.length <= 0 && props.localize;
}

function editSlug() {
    emitGenerateFromTitle(false);
    edit.value = true;
    locked.value = false;
    buttonText.value = props.labels.button_edit;
    icon.value = 'pencil-alt';
}

function lockSlug() {
    emitGenerateFromTitle(false);
    val.value = slugify(val.value) ?? '';
    edit.value = false;
    locked.value = true;
    buttonText.value = props.labels.button_locked;
    icon.value = 'lock';
}

function generateSlug() {
    val.value = slugify(getGeneratedSourceValue(' ')) ?? '';
    emitGenerateFromTitle(true);

    edit.value = false;
    locked.value = false;
    buttonText.value = props.labels.button_unlocked;
    icon.value = 'unlock';
}

function getGeneratedSourceValue(separator = '') {
    return generateSourceFields.value
        .map(element => {
            const el = document.querySelector(`input[name='fields[${element}]']`) as HTMLInputElement | null;
            return el?.value ?? '';
        })
        .join(separator);
}

function parseGenerateSourceFields() {
    if (!props.generate) return [];
    return props.generate
        .split(',')
        .map(element => element.trim())
        .filter(Boolean);
}

function emitGenerateFromTitle(active: boolean) {
    eventBus.emit('generate-from-title', {
        sources: generateSourceFields.value,
        active,
    });
}

function generateSlugFromSource(data: { source: string }) {
    if (generateSourceFields.value.includes(data.source)) {
        generateSlug();
    }
}
</script>
