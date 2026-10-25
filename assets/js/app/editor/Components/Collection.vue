<template>
    <div :id="name" ref="collectionContainer" class="collection-container">
        <div class="expand-buttons">
            <span>{{ labels.field_label }}:</span>

            <div class="btn-group" role="group">
                <button class="btn btn-secondary btn-sm collection-expand-all">
                    <i class="fas fa-fw fa-expand-alt"></i>
                    {{ labels.expand_all }}
                </button>
                <button class="btn btn-secondary btn-sm collection-collapse-all">
                    <i class="fas fa-fw fa-compress-alt"></i>
                    {{ labels.collapse_all }}
                </button>
            </div>
        </div>

        <div
            v-for="element in elements"
            :key="element.hash"
            class="collection-item"
            :data-collection-hash="element.hash"
            :class="{ collapsed: variant !== 'expanded' }"
        >
            <div class="d-block summary">
                <div class="card-header d-flex align-items-center">
                    <!-- Initial title. This is replaced by dynamic title in JS below. -->
                    <i class="card-marker-caret fa fa-caret-right"></i>
                    <span class="badge bg-secondary inline" :title="element.label">
                        <i :class="[element.icon, 'fas']"></i>
                    </span>
                    <div class="collection-item-title" :data-label="element.label">
                        {{ element.label }}
                    </div>
                    <!-- Navigation buttons -->
                    <component :is="compile(element.buttons)"></component>
                </div>
            </div>
            <div class="card details">
                <!-- The actual field -->
                <component :is="compile(element.content)" class="card-body"></component>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <p v-if="templates.length > 1" class="mt-4 mb-1">{{ labels.add_collection_item }}:</p>
                <div v-if="templates.length > 1" class="dropdown">
                    <button
                        :id="name + '-dropdownMenuButton'"
                        :disabled="!allowMore"
                        class="btn btn-secondary dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <i class="fas fa-fw fa-plus"></i> {{ labels.select }}
                    </button>
                    <div class="dropdown-menu" :aria-labelledby="name + '-dropdownMenuButton'">
                        <button
                            v-for="template in templates"
                            :key="template.label"
                            class="dropdown-item"
                            type="button"
                            :data-template="template.label"
                            @click="addCollectionItem($event)"
                        >
                            <i :class="[template.icon, 'fas fa-fw']" />
                            {{ template.label }}
                        </button>
                    </div>
                </div>
                <button
                    v-else
                    type="button"
                    :disabled="!allowMore"
                    class="btn btn-secondary btn-small"
                    :data-template="templates[0].label"
                    @click="addCollectionItem($event)"
                >
                    <i :class="[templates[0].icon, 'fas fa-fw']" />
                    {{ labels.add_collection_item }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUpdated, onBeforeUnmount } from 'vue';
import $ from 'jquery';
import { uniqid } from 'locutus/php/misc/uniqid';

type CollectionTemplate = {
    hash: string;
    label: string;
    icon: string;
    buttons: string;
    content: string;
};

type CompiledTemplate = {
    template: string;
};

type CollectionItem = ReturnType<typeof $>;

const props = defineProps<{
    name: string;
    templates: CollectionTemplate[];
    existingFields?: CollectionTemplate[];
    labels: Record<string, string>;
    limit: number;
    variant: string;
}>();

const collectionContainer = ref<HTMLElement | null>(null);

const elements = ref(props.existingFields || []);
const counter = ref((props.existingFields || []).length);

const selector = {
    collectionContainer: '#' + props.name,
    item: ' .collection-item',
    remove: ' .action-remove-collection-item',
    moveUp: ' .action-move-up-collection-item',
    moveDown: ' .action-move-down-collection-item',
    expandAll: ' .collection-expand-all',
    collapseAll: ' .collection-collapse-all',
    editor: ' #editor',
};
const eventNamespace = `.editorCollection-${props.name.replace(/[^A-Za-z0-9_-]/g, '-')}`;

const allowMore = computed(() => {
    return counter.value < props.limit;
});

const compiledTemplates = new Map<string, CompiledTemplate>();

function compile(element: string) {
    if (!compiledTemplates.has(element)) {
        compiledTemplates.set(element, { template: element });
    }
    return compiledTemplates.get(element);
}

function setAllButtonsStates(container: CollectionItem) {
    container.children(selector.item).each(function (this: HTMLElement) {
        setButtonsState($(this));
    });
}

function setButtonsState(item: CollectionItem) {
    //by default, enable
    item.find(selector.moveUp).first().removeAttr('disabled');
    item.find(selector.moveDown).first().removeAttr('disabled');
    if (!getPreviousCollectionItem(item)) {
        // first in collection
        item.find(selector.moveUp).first().attr('disabled', 'disabled');
    }
    if (!getNextCollectionItem(item)) {
        // last in collection
        item.find(selector.moveDown).first().attr('disabled', 'disabled');
    }
}

function getPreviousCollectionItem(item: CollectionItem) {
    return item.prev('.collection-item').length === 0 ? false : item.prev('.collection-item');
}

function getNextCollectionItem(item: CollectionItem) {
    return item.next('.collection-item').length === 0 ? false : item.next('.collection-item');
}

function getCollectionItemFromPressedButton(button: HTMLElement) {
    return $(button).closest('.collection-item').last();
}

function removeCollectionItem(button: HTMLElement) {
    const item = getCollectionItemFromPressedButton(button);
    const hash = item.attr('data-collection-hash');
    const index = elements.value.findIndex(element => element.hash === hash);

    if (index === -1) {
        return;
    }

    elements.value.splice(index, 1);
    counter.value--;
}

function addCollectionItem(event: Event) {
    // duplicate template without reference
    const selectedTemplate = getSelectedTemplate(event);
    if (!selectedTemplate) return;
    let template: CollectionTemplate = { ...selectedTemplate };
    const realhash = uniqid();
    template.content = template.content.replace(new RegExp(template.hash, 'g'), realhash);
    template.hash = realhash;
    elements.value.push(template);
    counter.value++;
}

function getSelectedTemplate(event: Event) {
    const target = $(event.target as HTMLElement).attr('data-template')
        ? $(event.target as HTMLElement)
        : $(event.target as HTMLElement).closest('[data-template]');
    const selectValue = target.attr('data-template');
    return props.templates.find(template => template.label === selectValue);
}

function updateTitle(item: HTMLElement) {
    const label = $(item).find('.collection-item-title').first();
    const input = $(item).find('textarea,input[type="text"]').first();
    const title = document.createElement('span');
    title.innerHTML = $(input).val() ? ($(input).val() as string) : (label.attr('data-label') as string);
    label.html(title.innerText);
}

onMounted(() => {
    if (collectionContainer.value) {
        setAllButtonsStates($(collectionContainer.value));
    }

    // Bind jQuery events
    $(document).on(
        `click${eventNamespace}`,
        selector.collectionContainer + ' .collection-item .summary',
        function (this: HTMLElement, e: Event) {
            e.preventDefault();
            let thisCollectionItem = getCollectionItemFromPressedButton(this as HTMLElement);
            thisCollectionItem.toggleClass('collapsed');
        },
    );

    $(document).on(
        `click${eventNamespace}`,
        selector.collectionContainer + selector.remove,
        function (this: HTMLElement, e: Event) {
            e.preventDefault();
            e.stopPropagation();
            let container = $(this).closest(selector.collectionContainer);
            let button = this as HTMLElement;

            $('#modalButtonAccept')
                .off(`click${eventNamespace}`)
                .one(`click${eventNamespace}`, function (this: HTMLElement) {
                    removeCollectionItem(button);
                    setAllButtonsStates(container);
                });
        },
    );

    $(document).on(
        `click${eventNamespace}`,
        selector.collectionContainer + selector.moveUp,
        function (this: HTMLElement, e: Event) {
            e.preventDefault();
            e.stopPropagation();
            let thisCollectionItem = getCollectionItemFromPressedButton(this as HTMLElement);
            let prevCollectionitem = getPreviousCollectionItem(thisCollectionItem);
            if (prevCollectionitem) {
                $(thisCollectionItem).after(prevCollectionitem);
                setButtonsState(thisCollectionItem);
                setButtonsState(prevCollectionitem);
            }
        },
    );

    $(document).on(
        `click${eventNamespace}`,
        selector.collectionContainer + selector.moveDown,
        function (this: HTMLElement, e: Event) {
            e.preventDefault();
            e.stopPropagation();
            let thisCollectionItem = getCollectionItemFromPressedButton(this as HTMLElement);
            let nextCollectionItem = getNextCollectionItem(thisCollectionItem);
            if (nextCollectionItem) {
                $(thisCollectionItem).before(nextCollectionItem);
                setButtonsState(thisCollectionItem);
                setButtonsState(nextCollectionItem);
            }
        },
    );

    $(document).on(
        `click${eventNamespace}`,
        selector.collectionContainer + selector.expandAll,
        function (this: HTMLElement, e: Event) {
            e.preventDefault();
            const collection = $(e.target as HTMLElement).closest(selector.collectionContainer);
            collection.find('.collection-item').removeClass('collapsed');
        },
    );

    $(document).on(
        `click${eventNamespace}`,
        selector.collectionContainer + selector.collapseAll,
        function (this: HTMLElement, e: Event) {
            e.preventDefault();
            const collection = $(e.target as HTMLElement).closest(selector.collectionContainer);
            collection.find('.collection-item').addClass('collapsed');
        },
    );

    $.each($(selector.collectionContainer + selector.item), function (this: HTMLElement) {
        updateTitle(this as HTMLElement);
    });

    $(selector.collectionContainer).on(
        `keyup${eventNamespace} change${eventNamespace}`,
        selector.item,
        function (this: HTMLElement) {
            updateTitle(this as HTMLElement);
        },
    );
});

onUpdated(() => {
    if (collectionContainer.value) {
        setAllButtonsStates($(collectionContainer.value));
    }
});

onBeforeUnmount(() => {
    $(document).off(eventNamespace);
    $(selector.collectionContainer).off(eventNamespace);
    $('#modalButtonAccept').off(eventNamespace);
});

defineExpose({
    elements,
    counter,
    allowMore,
    compile,
    removeCollectionItem,
    addCollectionItem,
});
</script>
