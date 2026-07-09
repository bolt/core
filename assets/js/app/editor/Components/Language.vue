<template>
    <div id="multiselect-localeswitcher" class="form-group">
        <span class="form-label d-block">{{ label }}</span>
        <multiselect
            v-model="locale"
            track-by="name"
            label="localizedname"
            :options="locales || []"
            :searchable="false"
            :show-labels="false"
            :limit="1"
            @update:model-value="switchLocale"
        >
            <template #singleLabel="slotProps">
                <span class="fp me-1" :class="slotProps.option.flag"></span>
                <span>
                    {{ slotProps.option.name }}
                    <small style="white-space: nowrap">({{ slotProps.option.code }})</small>
                </span>
            </template>
            <template #option="slotProps">
                <span class="fp me-1" :class="slotProps.option.flag"></span>
                <span>
                    {{ slotProps.option.name }}
                    <small style="white-space: nowrap">({{ slotProps.option.code }})</small>
                </span>
            </template>
        </multiselect>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import Multiselect from 'vue-multiselect';

type LocaleOption = {
    code: string;
    name: string;
    localizedname: string;
    flag?: string;
    link: string;
};

const props = defineProps<{
    label?: string;
    locales?: LocaleOption[];
    current?: string;
}>();

const locale = ref<LocaleOption | null>(null);

onMounted(() => {
    const localesList = props.locales || [];
    if (props.current) {
        const currentLocale = localesList.find(l => l.code === props.current);
        if (currentLocale) {
            locale.value = currentLocale;
        } else {
            locale.value = localesList[0] ?? null;
        }
    } else {
        locale.value = localesList[0] ?? null;
    }
});

function switchLocale(selected: LocaleOption | null) {
    if (!selected) return;
    const link = selected.link + location.hash;
    globalThis.location.href = link;
    return link;
}

defineExpose({
    locale,
    switchLocale,
});
</script>
