<template>
    <div :id="`multiselect-${id}`" :class="classname" class="multiselect-bolt-wrapper">
        <multiselect
            ref="vselect"
            v-model="selected"
            :limit="1000"
            :multiple="multiple"
            :options="selectOptions"
            :options-limit="optionslimit"
            :searchable="autocomplete || taggable"
            :show-labels="false"
            :taggable="taggable"
            :disabled="readonly"
            :data-errormessage="errormessage || undefined"
            label="value"
            tag-placeholder="Add this as new tag"
            tag-position="bottom"
            track-by="key"
            :loading="isLoading"
            @tag="addTag"
        >
            <template v-if="name === 'status'" #singleLabel="slotProps">
                <span class="status me-2" :class="`is-${slotProps.option.key}`"></span>
                {{ rawFilter(slotProps.option.value) }}
            </template>
            <template v-else-if="hasRecordLinks" #singleLabel="slotProps">
                <!-- eslint-disable vue/no-v-html -->
                <span v-html="slotProps.option.value"></span>
                <!--eslint-enable-->
                <div v-if="slotProps.option.link_to_record_url" class="multiselect__tag__edit">
                    <a
                        :href="slotProps.option.link_to_record_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Edit record"
                    >
                        <i class="far fa-edit me-0"></i>
                    </a>
                </div>
            </template>

            <template v-if="name === 'status'" #option="slotProps">
                <span class="status me-2" :class="`is-${slotProps.option.key}`"></span>
                {{ rawFilter(slotProps.option.value) }}
            </template>

            <template v-if="name !== 'status'" #tag="slotProps">
                <!-- eslint-disable-next-line vuejs-accessibility/no-static-element-interactions -->
                <span
                    :class="{ empty: slotProps.option.value == '' }"
                    @drop="drop($event)"
                    @dragover="allowDrop($event)"
                >
                    <!-- eslint-disable-next-line vuejs-accessibility/no-static-element-interactions -->
                    <span
                        :id="slotProps.option.key"
                        :key="slotProps.option.value"
                        class="multiselect__tag"
                        :draggable="!taggable"
                        @dragstart="drag($event)"
                        @dragover="dragOver($event)"
                        @dragleave="dragLeave($event)"
                        @dragend="dragEnd($event)"
                    >
                        <div v-if="!taggable" class="multiselect__tag__drag">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <!-- eslint-disable-next-line vue/no-v-html -->
                        <span v-html="slotProps.option.value"></span>

                        <div v-if="slotProps.option.link_to_record_url" class="multiselect__tag__edit">
                            <a
                                :href="slotProps.option.link_to_record_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Edit record"
                            >
                                <i class="far fa-edit me-0"></i>
                            </a>
                        </div>

                        <i
                            role="button"
                            aria-label="Remove element"
                            tabindex="0"
                            class="multiselect__tag-icon"
                            @keypress.enter.prevent="removeElement(slotProps.option)"
                            @mousedown.prevent="removeElement(slotProps.option)"
                        ></i>
                    </span>
                </span>
            </template>
        </multiselect>
        <input :id="id" type="hidden" :name="name" :form="form" :value="sanitized" />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import Multiselect from 'vue-multiselect';
import $ from 'jquery';
import { raw } from '../../../filters/string';
import type { SelectOption, SelectValue, SelectedValue } from '../types';

type SelectCache = Record<string, SelectOption[]>;
type SelectRequest = PromiseLike<SelectOption[]>;
type RequestCache = Partial<Record<string, SelectRequest>>;
type SelectWindow = Window &
    typeof globalThis & {
        selectCache?: SelectCache;
        requestCache?: RequestCache;
    };
type RemovableMultiselect = {
    removeElement(element: SelectOption): void;
};

const props = defineProps<{
    value?: SelectValue;
    name?: string;
    id?: string;
    form?: string;
    options?: SelectOption[];
    optionslimit?: number;
    multiple?: boolean;
    taggable?: boolean;
    readonly?: boolean;
    classname?: string;
    autocomplete?: boolean;
    errormessage?: string | boolean;
    required?: string | boolean;
    fetchurl?: string;
}>();

const vselect = ref<RemovableMultiselect | null>(null);

const selected = ref<SelectedValue>([]);
const isLoading = ref(false);
const selectOptions = ref<SelectOption[]>(props.options || []);

const sanitized = computed(() => {
    if (selected.value === null) {
        return JSON.stringify([]);
    } else if (Array.isArray(selected.value)) {
        const filtered = selected.value.map(item => item.key);
        return JSON.stringify(filtered);
    } else {
        return JSON.stringify([selected.value.key]);
    }
});

const fieldName = computed(() => {
    return props.name + '[]';
});

const hasRecordLinks = computed(() => {
    return selectOptions.value.some(option => option && option.link_to_record_url);
});

function fixSelectedItems() {
    let _values: Array<string | number> = [];
    // Explicit nullish check, not truthiness: a numeric key of `0` is a valid
    // value that `if (props.value)` would wrongly treat as "no selection".
    if (props.value !== undefined && props.value !== null) {
        _values = Array.isArray(props.value) ? props.value : [props.value];
    }

    let filterSelectedItems: SelectOption[] = _values
        .map(val => selectOptions.value.find(opt => opt.key === val))
        .filter((item): item is SelectOption => item !== undefined);

    if (!!props.required && filterSelectedItems.length === 0) {
        const firstOption = selectOptions.value[0];
        filterSelectedItems = firstOption ? [firstOption] : [];
    }

    // vue-multiselect expects a single object (or null) when `multiple` is false.
    // Initializing with an array there leaves `addTag` pushing into it, so a
    // single-select field would accumulate multiple tags. Bind the shape the
    // mode actually expects.
    selected.value = props.multiple ? filterSelectedItems : (filterSelectedItems[0] ?? null);
}

onMounted(() => {
    if (props.fetchurl) {
        const cacheWindow = window as SelectWindow;
        const selectCache = (cacheWindow.selectCache = cacheWindow.selectCache || {});
        const requestCache = (cacheWindow.requestCache = cacheWindow.requestCache || {});

        if (selectCache[props.fetchurl]) {
            selectOptions.value = selectCache[props.fetchurl];
            fixSelectedItems();
        } else {
            const cachedRequest = requestCache[props.fetchurl];
            if (cachedRequest) {
                cachedRequest.then(response => {
                    selectOptions.value = response;
                    fixSelectedItems();
                });
                return;
            }

            isLoading.value = true;

            requestCache[props.fetchurl] = $.ajax({
                url: props.fetchurl,
                dataType: 'json',
                cache: true,
            }) as SelectRequest;
            requestCache[props.fetchurl]?.then(response => {
                selectOptions.value = response;
                selectCache[props.fetchurl as string] = response;
                isLoading.value = false;
                fixSelectedItems();
            });
        }
    } else {
        fixSelectedItems();
    }
});

function addTag(newTag: string) {
    const tag: SelectOption = {
        key: newTag,
        value: newTag,
        selected: true,
    };
    selectOptions.value.push(tag);
    if (Array.isArray(selected.value)) {
        selected.value.push(tag);
    } else {
        // Single-select (taggable, non-multiple): the new tag becomes the single
        // selection, so bind the object directly rather than wrapping it in an array.
        selected.value = tag;
    }
}

function removeElement(element: SelectOption) {
    if (vselect.value) {
        vselect.value.removeElement(element);
    }
}

function findDropElement(el: HTMLElement): HTMLElement {
    while (!el.hasAttribute('draggable') && el.parentNode) {
        el = el.parentNode as HTMLElement;
    }
    return el;
}

function drop(e: DragEvent) {
    e.preventDefault();

    const incomingId = e.dataTransfer?.getData('text');
    const outgoingId = findDropElement(e.target as HTMLElement).id;

    if (!Array.isArray(selected.value)) {
        return;
    }

    const incomingElement = selected.value.find(el => '' + el.key === '' + incomingId);
    const outgoingElement = selected.value.find(el => '' + el.key === '' + outgoingId);

    if (!incomingElement || !outgoingElement) {
        return;
    }

    const incomingIndex = selected.value.indexOf(incomingElement);
    const outgoingIndex = selected.value.indexOf(outgoingElement);

    if (incomingIndex === -1 || outgoingIndex === -1) {
        return;
    }

    const newPosition = incomingIndex < outgoingIndex ? outgoingIndex + 1 : outgoingIndex;

    selected.value.splice(incomingIndex, 1);
    selected.value.splice(newPosition, 0, incomingElement);
}

function allowDrop(e: DragEvent) {
    e.preventDefault();
}

function drag(e: DragEvent) {
    $(e.target as HTMLElement).addClass('dragging');
    e.dataTransfer?.setData('text', (e.target as HTMLElement).id);
}

function dragOver(e: DragEvent) {
    const target = findDropElement(e.target as HTMLElement);
    if (!$(target).hasClass('dragging')) {
        $(target).addClass('dragover');
    }
}

function dragLeave(e: DragEvent) {
    const target = findDropElement(e.target as HTMLElement);
    $(target).removeClass('dragover');
}

function dragEnd(e: DragEvent) {
    const target = findDropElement(e.target as HTMLElement);
    $(target).removeClass('dragging');
}

function rawFilter(string: string) {
    return raw(string) || '';
}

defineExpose({
    vselect,
    selected,
    isLoading,
    selectOptions,
    sanitized,
    fieldName,
    hasRecordLinks,
    addTag,
    removeElement,
    drop,
    findDropElement,
    allowDrop,
    drag,
    dragOver,
    dragLeave,
    dragEnd,
    rawFilter,
});
</script>
