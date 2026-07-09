<template>
    <div>
        <input
            :id="id"
            v-model="rawVal"
            :title="name"
            class="form-control"
            :class="getType"
            :name="name"
            type="text"
            :disabled="disabled"
            :required="required"
            :readonly="readonly"
            :data-errormessage="typeof errormessage === 'string' ? errormessage : undefined"
            :pattern="typeof pattern === 'string' ? pattern : undefined"
            :placeholder="typeof placeholder === 'string' ? placeholder : undefined"
            :autofocus="autofocus == true"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { eventBus } from '../../eventBus';
import { useFieldValue } from '../composables/useFieldValue';

const props = defineProps<{
    // Usually a string, but numeric on the media edit page (cropX/cropY/cropZoom),
    // where media/edit.html.twig passes raw numbers into text.html.twig.
    value?: string | number;
    name?: string;
    type?: string;
    disabled?: boolean;
    id?: string;
    required?: boolean;
    readonly?: boolean;
    errormessage?: string | boolean;
    pattern?: string | boolean;
    placeholder?: string | boolean;
    autofocus?: boolean;
}>();

const { rawVal } = useFieldValue(props.value);

const generate = ref(false);

const getType = computed(() => (props.type === 'large' ? 'form-control-lg' : props.type));

watch(rawVal, () => {
    if (generate.value) {
        eventBus.emit('slugify-from-title', { source: fieldKey.value });
    }
});

const fieldKey = computed(() => {
    const match = props.name?.match(/^fields\[([^\]]+)]/);
    return match ? match[1] : props.name || '';
});

const onGenerateFromTitle = (data: { sources: string[]; active: boolean }) => {
    if (data.sources.includes(fieldKey.value)) {
        generate.value = data.active;
    }
};

onMounted(() => eventBus.on('generate-from-title', onGenerateFromTitle));
onBeforeUnmount(() => eventBus.off('generate-from-title', onGenerateFromTitle));
</script>
