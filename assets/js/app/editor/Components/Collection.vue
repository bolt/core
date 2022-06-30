<template>
    <div :id="name" ref="collectionContainer" class="collection-container">
        <div class="expand-buttons">
            <label>{{ labels.field_label }}:</label>

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
                    <div :is="compile(element.buttons)"></div>
                </div>
            </div>
            <div class="card details">
                <!-- The actual field -->
                <div :is="compile(element.content)" class="card-body"></div>
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
                        <a
                            v-for="template in templates"
                            :key="template.label"
                            class="dropdown-item"
                            :data-template="template.label"
                            @click="addCollectionItem($event)"
                        >
                            <i :class="[template.icon, 'fas fa-fw']" />
                            {{ template.label }}
                        </a>
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

<script>
import Vue from 'vue';
import $ from 'jquery';
var uniqid = require('locutus/php/misc/uniqid');
export default {
    name: 'EditorCollection',
    props: {
        name: {
            type: String,
            required: true,
        },
        templates: {
            type: Array,
            required: true,
        },
        existingFields: {
            type: Array,
        },
        labels: {
            type: Object,
            required: true,
        },
        limit: {
            type: Number,
            required: true,
        },
        variant: {
            type: String,
            required: true,
        },
    },
    data() {
        let templateSelectOptions = [];
        return {
            elements: this.existingFields,
            counter: this.existingFields.length,
            templateSelectName: 'templateSelect' + this.id,
            templateSelectOptions: templateSelectOptions,
            selector: {
                collectionContainer: '#' + this.name,
                item: ' .collection-item',
                remove: ' .action-remove-collection-item',
                moveUp: ' .action-move-up-collection-item',
                moveDown: ' .action-move-down-collection-item',
                expandAll: ' .collection-expand-all',
                collapseAll: ' .collection-collapse-all',
                editor: ' #editor',
            },
        };
    },
    computed: {
        initialSelectValue() {
            return this.templateSelectOptions[0].key;
        },
        allowMore: function() {
            return this.counter < this.limit;
        },
    },
    mounted() {
        this.setAllButtonsStates(window.$(this.$refs.collectionContainer));
        let vueThis = this;
        /**
         * Event listeners on collection items buttons
         * This is a jQuery event listener, because Vue cannot handle an event emitted by a non-vue element.
         * The collection items are not Vue elements in order to initialise them correctly within their twig template.
         */
        window
            .$(document)
            .on('click', vueThis.selector.collectionContainer + ' .collection-item .summary', function(e) {
                e.preventDefault();
                let thisCollectionItem = vueThis.getCollectionItemFromPressedButton(this);
                thisCollectionItem.toggleClass('collapsed');
            });
        window.$(document).on('click', vueThis.selector.collectionContainer + vueThis.selector.remove, function(e) {
            e.preventDefault();
            e.stopPropagation();
            let collectionContainer = window.$(this).closest(vueThis.selector.collectionContainer);
            let button = this;

            $('#modalButtonAccept').on('click', function() {
                vueThis.getCollectionItemFromPressedButton(button).remove();
            });
            vueThis.setAllButtonsStates(collectionContainer);
            vueThis.counter--;
        });
        window.$(document).on('click', vueThis.selector.collectionContainer + vueThis.selector.moveUp, function(e) {
            e.preventDefault();
            e.stopPropagation();
            let thisCollectionItem = vueThis.getCollectionItemFromPressedButton(this);
            let prevCollectionitem = vueThis.getPreviousCollectionItem(thisCollectionItem);
            window.$(thisCollectionItem).after(prevCollectionitem);
            vueThis.setButtonsState(thisCollectionItem);
            vueThis.setButtonsState(prevCollectionitem);
        });
        window.$(document).on('click', vueThis.selector.collectionContainer + vueThis.selector.moveDown, function(e) {
            e.preventDefault();
            e.stopPropagation();
            let thisCollectionItem = vueThis.getCollectionItemFromPressedButton(this);
            let nextCollectionItem = vueThis.getNextCollectionItem(thisCollectionItem);
            window.$(thisCollectionItem).before(nextCollectionItem);
            vueThis.setButtonsState(thisCollectionItem);
            vueThis.setButtonsState(nextCollectionItem);
        });
        window.$(document).on('click', vueThis.selector.collectionContainer + vueThis.selector.expandAll, function(e) {
            e.preventDefault();
            const collection = $(e.target).closest(vueThis.selector.collectionContainer);
            collection.find('.collection-item').removeClass('collapsed');
        });
        window
            .$(document)
            .on('click', vueThis.selector.collectionContainer + vueThis.selector.collapseAll, function(e) {
                e.preventDefault();
                const collection = $(e.target).closest(vueThis.selector.collectionContainer);
                collection.find('.collection-item').addClass('collapsed');
            });
        /**
         * Update the title dynamically.
         */
        $(document).ready(function() {
            $.each(window.$(vueThis.selector.collectionContainer + vueThis.selector.item), function() {
                updateTitle(this);
            });
            window.$(vueThis.selector.collectionContainer).on('keyup change', vueThis.selector.item, function() {
                updateTitle(this);
            });
        });
        /**
         * Pass a .collection-item element to update the title
         * with the value of the first text-based field.
         */
        function updateTitle(item) {
            const label = $(item)
                .find('.collection-item-title')
                .first();
            const input = $(item)
                .find('textarea,input[type="text"]')
                .first();
            // We use this 'innerText' trick to ensure the title is plain text.
            var title = document.createElement('span');
            title.innerHTML = $(input).val() ? $(input).val() : label.attr('data-label');
            label.html(title.innerText);
        }
        /**
         * Open newly inserted collection items.
         */
        // $(document).on('DOMNodeInserted', function(e) {
        //     if ($(e.target).hasClass('collection-item')) {
        //         $(e.target)
        //             .find('details')
        //             .first()
        //             .attr('open', '');
        //     }
        // });
    },
    updated() {
        this.setAllButtonsStates(window.$(this.$refs.collectionContainer));
    },
    methods: {
        compile(element) {
            return Vue.compile(element);
        },
        setAllButtonsStates(collectionContainer) {
            let vueThis = this;
            collectionContainer.children(vueThis.selector.item).each(function() {
                vueThis.setButtonsState(window.$(this));
            });
        },
        setButtonsState(item) {
            //by default, enable
            item.find(this.selector.moveUp)
                .first()
                .removeAttr('disabled');
            item.find(this.selector.moveDown)
                .first()
                .removeAttr('disabled');
            if (!this.getPreviousCollectionItem(item)) {
                // first in collection
                item.find(this.selector.moveUp)
                    .first()
                    .attr('disabled', 'disabled');
            }
            if (!this.getNextCollectionItem(item)) {
                // last in collection
                item.find(this.selector.moveDown)
                    .first()
                    .attr('disabled', 'disabled');
            }
        },
        getPreviousCollectionItem(item) {
            return item.prev('.collection-item').length === 0 ? false : item.prev('.collection-item');
        },
        getNextCollectionItem(item) {
            return item.next('.collection-item').length === 0 ? false : item.next('.collection-item');
        },
        getCollectionItemFromPressedButton(button) {
            return window
                .$(button)
                .closest('.collection-item')
                .last();
        },
        addCollectionItem(event) {
            // duplicate template without reference
            let template = $.extend(true, {}, this.getSelectedTemplate(event));
            const realhash = uniqid();
            template.content = template.content.replace(new RegExp(template.hash, 'g'), realhash);
            template.hash = realhash;
            this.elements.push(template);
            this.counter++;
        },
        getSelectedTemplate(event) {
            const target = $(event.target).attr('data-template')
                ? $(event.target)
                : $(event.target).closest('[data-template]');
            let selectValue = target.attr('data-template');
            return this.templates.find(template => template.label === selectValue);
        },
    },
};
</script>
