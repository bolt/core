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
                    <span class="badge badge-secondary inline" :title="element.label">
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
                <div :is="currentComponent" class="card-body"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <p v-if="getObjLength(fields) > 1" class="mt-4 mb-1">{{ labels.add_collection_item }}:</p>
                <div v-if="getObjLength(fields) > 1" class="dropdown">
                    <button
                        :id="name + '-dropdownMenuButton'"
                        :disabled="!allowMore"
                        class="btn btn-secondary dropdown-toggle"
                        type="button"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <i class="fas fa-fw fa-plus"></i> {{ labels.select }}
                    </button>
                    <div class="dropdown-menu" :aria-labelledby="name + '-dropdownMenuButton'">
                        <a
                            v-for="field in fields"
                            :key="field.type"
                            class="dropdown-item"
                            :data-field="field.type"
                            @click="addCollectionItem(field.slug)"
                        >
                            <i :class="[field.icon, 'fas fa-fw']" />
                            {{ field.label }}
                        </a>
                    </div>
                </div>
                <button
                    v-else
                    type="button"
                    class="btn btn-secondary btn-small"
                    :data-field="fields[0].slug"
                    @click="addCollectionItem(fields.slug)"
                >
                    <i :class="[fields[0].icon, 'fas fa-fw']" />
                    {{ labels.add_collection_item }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { compile } from 'vue';
/*
Editor Components for rendering
 */
import Text from './Text';
import Slug from './Slug';
import Date from './Date';
import Select from './Select';
import Number from './Number';
import Html from './Html';
import Markdown from './Markdown';
import Textarea from './Textarea';
import Embed from './Embed';
import Image from './Image';
import Imagelist from './Imagelist';
import Email from './Email';
import Password from './Password';
import ThemeSelect from './ThemeSelect';
import Language from './Language';
import File from './File';
import Filelist from './Filelist';
import Checkbox from './Checkbox';

import $ from 'jquery';

export default {
    name: 'EditorCollection',
    components: {
        Text,
        Slug,
        Date,
        Select,
        Number,
        Html,
        Markdown,
        Textarea,
        Embed,
        Image,
        Imagelist,
        Email,
        Password,
        ThemeSelect,
        Language,
        File,
        Filelist,
        Checkbox,
    },
    props: {
        name: {
            type: String,
            required: true,
        },
        fields: {
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
            required: true,
        },
        variant: {
            type: String,
            required: true,
        },
    },
    data() {
        let fieldSelectOptions = [];
        return {
            currentComponent: '',
            elements: this.existingFields,
            counter: this.getObjLength(this.existingFields),
            fieldSelectName: 'fieldSelect' + this.id,
            fieldSelectOptions: fieldSelectOptions,
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
            return this.fieldSelectOptions[0].key;
        },
        allowMore: function () {
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
            .on('click', vueThis.selector.collectionContainer + ' .collection-item .summary', function (e) {
                e.preventDefault();
                let thisCollectionItem = vueThis.getCollectionItemFromPressedButton(this);
                thisCollectionItem.toggleClass('collapsed');
            });
        window.$(document).on('click', vueThis.selector.collectionContainer + vueThis.selector.remove, function (e) {
            e.preventDefault();
            e.stopPropagation();
            let collectionContainer = window.$(this).closest(vueThis.selector.collectionContainer);
            vueThis.getCollectionItemFromPressedButton(this).remove();
            vueThis.setAllButtonsStates(collectionContainer);
            vueThis.counter--;
        });
        window.$(document).on('click', vueThis.selector.collectionContainer + vueThis.selector.moveUp, function (e) {
            e.preventDefault();
            e.stopPropagation();
            let thisCollectionItem = vueThis.getCollectionItemFromPressedButton(this);
            let prevCollectionitem = vueThis.getPreviousCollectionItem(thisCollectionItem);
            window.$(thisCollectionItem).after(prevCollectionitem);
            vueThis.setButtonsState(thisCollectionItem);
            vueThis.setButtonsState(prevCollectionitem);
        });
        window.$(document).on('click', vueThis.selector.collectionContainer + vueThis.selector.moveDown, function (e) {
            e.preventDefault();
            e.stopPropagation();
            let thisCollectionItem = vueThis.getCollectionItemFromPressedButton(this);
            let nextCollectionItem = vueThis.getNextCollectionItem(thisCollectionItem);
            window.$(thisCollectionItem).before(nextCollectionItem);
            vueThis.setButtonsState(thisCollectionItem);
            vueThis.setButtonsState(nextCollectionItem);
        });
        window.$(document).on('click', vueThis.selector.collectionContainer + vueThis.selector.expandAll, function (e) {
            e.preventDefault();
            const collection = $(e.target).closest(vueThis.selector.collectionContainer);
            collection.find('.collection-item').removeClass('collapsed');
        });
        window
            .$(document)
            .on('click', vueThis.selector.collectionContainer + vueThis.selector.collapseAll, function (e) {
                e.preventDefault();
                const collection = $(e.target).closest(vueThis.selector.collectionContainer);
                collection.find('.collection-item').addClass('collapsed');
            });
        /**
         * Update the title dynamically.
         */
        $(document).ready(function () {
            $.each(window.$(vueThis.selector.collectionContainer + vueThis.selector.item), function () {
                updateTitle(this);
            });
            window.$(vueThis.selector.collectionContainer).on('keyup change', vueThis.selector.item, function () {
                updateTitle(this);
            });
        });
        /**
         * Pass a .collection-item element to update the title
         * with the value of the first text-based field.
         */
        function updateTitle(item) {
            const label = $(item).find('.collection-item-title').first();
            const input = $(item).find('textarea,input[type="text"]').first();
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
        getObjLength(obj) {
            return Object.keys(obj).length;
        },
        log(item) {
            console.log(item);
        },
        compile(element) {
            return compile(element);
        },
        setAllButtonsStates(collectionContainer) {
            let vueThis = this;
            collectionContainer.children(vueThis.selector.item).each(function () {
                vueThis.setButtonsState(window.$(this));
            });
        },
        setButtonsState(item) {
            //by default, enable
            item.find(this.selector.moveUp).first().removeAttr('disabled');
            item.find(this.selector.moveDown).first().removeAttr('disabled');
            if (!this.getPreviousCollectionItem(item)) {
                // first in collection
                item.find(this.selector.moveUp).first().attr('disabled', 'disabled');
            }
            if (!this.getNextCollectionItem(item)) {
                // last in collection
                item.find(this.selector.moveDown).first().attr('disabled', 'disabled');
            }
        },
        getPreviousCollectionItem(item) {
            return item.prev('.collection-item').length === 0 ? false : item.prev('.collection-item');
        },
        getNextCollectionItem(item) {
            return item.next('.collection-item').length === 0 ? false : item.next('.collection-item');
        },
        getCollectionItemFromPressedButton(button) {
            return window.$(button).closest('.collection-item').last();
        },
        addCollectionItem(fieldName) {
            console.log(this.fields);
            this.counter++;

            // Create switch case for every field type
            switch (fieldName) {
                case 'text':
                    var component = Text;
                    break;
                case 'slug':
                    this.elements.push(Slug);
                    this.currentComponent = Slug;
                    break;
                case 'date':
                    this.elements.push(Date);
                    this.currentComponent = Date;
                    break;
                case 'select':
                    this.elements.push(Select);
                    this.currentComponent = Select;
                    break;
                case 'number':
                    this.elements.push(Number);
                    this.currentComponent = Number;
                    break;
                case 'html':
                    this.elements.push(Html);
                    this.currentComponent = Html;
                    break;
                case 'markdown':
                    this.elements.push(Markdown);
                    this.currentComponent = Markdown;
                    break;
                case 'textarea':
                    this.elements.push(Textarea);
                    this.currentComponent = Textarea;
                    break;
                case 'embed':
                    this.elements.push(Embed);
                    this.currentComponent = Embed;
                    break;
                case 'image':
                    this.elements.push(Image);
                    this.currentComponent = Image;
                    break;
                case 'imagelist':
                    this.elements.push(Imagelist);
                    this.currentComponent = Imagelist;
                    break;
                case 'email':
                    this.elements.push(Email);
                    this.currentComponent = Email;
                    break;
                case 'password':
                    this.elements.push(Password);
                    this.currentComponent = Password;
                    break;
                case 'themeSelect':
                    this.elements.push(ThemeSelect);
                    this.currentComponent = ThemeSelect;
                    break;
                case 'language':
                    this.elements.push(Language);
                    this.currentComponent = Language;
                    break;
                case 'file':
                    this.elements.push(File);
                    this.currentComponent = File;
                    break;
                case 'filelist':
                    this.elements.push(Filelist);
                    this.currentComponent = Filelist;
                    break;
                case 'checkbox':
                    this.elements.push(Checkbox);
                    this.currentComponent = Checkbox;
                    break;
            }
            this.currentComponent(component);
            this.elements.push(component);
            return this.currentComponent;
        },
        getSelectedField(event) {
            const target = $(event.target).attr('data-field')
                ? $(event.target)
                : $(event.target).closest('[data-field]');
            let selectValue = target.attr('data-field');
            return this.fields.find((field) => field.label === selectValue);
        },
    },
};
</script>
