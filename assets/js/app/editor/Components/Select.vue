<template>
    <div :id="`multiselect-${id}`" :class="classname" class="multiselect-bolt-wrapper">
        <multiselect
            ref="vselect"
            v-model="selected"
            :limit="1000"
            :multiple="multiple"
            :options="options"
            :options-limit="optionslimit"
            :searchable="autocomplete || taggable"
            :show-labels="false"
            :taggable="taggable"
            :disabled="readonly"
            :data-errormessage="errormessage"
            label="value"
            tag-placeholder="Add this as new tag"
            tag-position="bottom"
            track-by="key"
            :loading="isLoading"
            @tag="addTag"
        >
            <template v-if="name === 'status'" slot="singleLabel" slot-scope="props">
                <span class="status me-2" :class="`is-${props.option.key}`"></span>
                {{ props.option.value | raw }}
            </template>
            <template v-if="name === 'status'" slot="option" slot-scope="props">
                <span class="status me-2" :class="`is-${props.option.key}`"></span>
                {{ props.option.value | raw }}
            </template>

            <template v-if="props.option.link_to_record_url" slot="singleLabel" slot-scope="props">
                <span v-html="props.option.value"></span>
                <div class="multiselect__tag__edit">
                    <a :href="props.option.link_to_record_url" target="_blank" rel="noopener noreferrer">
                        <i class="far fa-edit me-0"></i>
                    </a>
                </div>
            </template>

            <template v-if="name !== 'status'" slot="tag" slot-scope="props">
                <span :class="{ empty: props.option.value == '' }" @drop="drop($event)" @dragover="allowDrop($event)">
                    <span
                        :id="props.option.key"
                        :key="props.option.value"
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
                        <span v-html="props.option.value"></span>

                        <div v-if="props.option.link_to_record_url" class="multiselect__tag__edit">
                            <a :href="props.option.link_to_record_url" target="_blank" rel="noopener noreferrer">
                                <i class="far fa-edit me-0"></i>
                            </a>
                        </div>

                        <i
                            tabindex="1"
                            class="multiselect__tag-icon"
                            @keypress.enter.prevent="removeElement(props.option)"
                            @mousedown.prevent="removeElement(props.option)"
                        ></i>
                    </span>
                </span>
            </template>
        </multiselect>
        <input :id="id" type="hidden" :name="name" :form="form" :value="sanitized" />
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect';
import $ from 'jquery';

export default {
    name: 'EditorSelect',
    components: { Multiselect },
    props: {
        value: Array | String,
        name: String,
        id: String,
        form: String,
        options: Array,
        optionslimit: Number,
        multiple: Boolean,
        taggable: Boolean,
        readonly: Boolean,
        classname: String,
        autocomplete: Boolean,
        errormessage: String | Boolean, //string if errormessage is set, and false otherwise
        required: String | Boolean,
        fetchurl: String,
    },
    data: () => {
        return {
            selected: [],
        };
    },
    computed: {
        sanitized() {
            let filtered;

            if (this.selected === null) {
                return JSON.stringify([]);
            } else if (this.selected.map) {
                filtered = this.selected.map(item => item.key);
                return JSON.stringify(filtered);
            } else {
                return JSON.stringify([this.selected.key]);
            }
        },
        fieldName() {
            return this.name + '[]';
        },
    },
    mounted() {
        /**
         * Filter method is necessary for required fields because the empty option is not
         * set. If the field is empty, "filterSelectedItems" will contain an undefined
         * element and "select" will not be filled with the first available option.
         */
        const fixSelectedItems = function() {
            const _values = !this.value ? [] : this.value.map ? this.value : [this.value];

            let filterSelectedItems = _values
                .map(value => {
                    const item = this.options.filter(opt => opt.key === value);
                    if (item.length > 0) {
                        return item[0];
                    }
                })
                .filter(item => undefined !== item);

            if (!!this._props.required && filterSelectedItems.length === 0) {
                filterSelectedItems = [this.options[0]];
            }

            this.selected = filterSelectedItems;
        };

        /**
         * If `fetchurl` is defined then pre-fill using a call. One problem is that on initialization
         * for existing selects, multiple requests will be done at the same time. Subsequent queries
         * can make use of the cache. Important part here is to have server-side caching.
         */
        if (this.fetchurl) {
            window.selectCache = window.selectCache || {};
            window.requestCache = window.requestCache || {};

            if (window.selectCache[this.fetchurl]) {
                this.options = window.selectCache[this.fetchurl];
                fixSelectedItems.call(this);
            } else if (window.requestCache[this.fetchurl]) {
                window.requestCache[this.fetchurl].then(response => {
                    this.options = response;
                    fixSelectedItems.call(this);
                });
            } else {
                this.isLoading = true;

                window.requestCache[this.fetchurl] = $.ajax({ url: this.fetchurl, dataType: 'json', cache: true });
                window.requestCache[this.fetchurl].then(
                    response => {
                        this.options = response;
                        window.selectCache[this.fetchurl] = response;
                        this.isLoading = false;
                        fixSelectedItems.call(this);
                    },
                );
            }
        } else {
            fixSelectedItems.call(this);
        }
    },
    methods: {
        addTag(newTag) {
            const tag = {
                key: newTag,
                value: newTag,
                selected: true,
            };
            this.options.push(tag);
            this.value.push(tag);
            this.selected.push(tag);
        },
        removeElement: function(element) {
            this.$refs.vselect.removeElement(element);
        },
        drop(e) {
            e.preventDefault();

            const incomingId = e.dataTransfer.getData('text');

            /**
             * JS Draggable API allows elements to be dropped inside child nodes
             * We have to find the parent with draggable='true' to get the id.
             */
            const outgoingId = this.findDropElement(e.target).id;

            const incomingElement = this.selected.find(el => '' + el.key === '' + incomingId);
            const outgoingElement = this.selected.find(el => '' + el.key === '' + outgoingId);

            const incomingIndex = this.selected.indexOf(incomingElement);
            const outgoingIndex = this.selected.indexOf(outgoingElement);

            // if dragging down, insert after. else, insert before.
            const newPosition = incomingIndex < outgoingIndex ? outgoingIndex + 1 : outgoingIndex;

            this.selected.splice(incomingIndex, 1);
            this.selected.splice(newPosition, 0, incomingElement);
        },
        findDropElement(el) {
            while (!el.hasAttribute('draggable')) {
                el = el.parentNode;
            }

            return el;
        },
        allowDrop(e) {
            e.preventDefault();
        },
        drag(e) {
            $(e.target).addClass('dragging');
            e.dataTransfer.setData('text', e.target.id);
        },
        dragOver(e) {
            const target = this.findDropElement(e.target);

            // Only add dragover if not dragging over the element
            // that is being dragged
            if (!$(target).hasClass('dragging')) {
                $(target).addClass('dragover');
            }
        },
        dragLeave(e) {
            const target = this.findDropElement(e.target);
            $(target).removeClass('dragover');
        },
        dragEnd(e) {
            const target = this.findDropElement(e.target);
            $(target).removeClass('dragging');
        },
    },
};
</script>
