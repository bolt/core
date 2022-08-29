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
                placeholder="…"
                type="text"
                :class="fieldClass"
                :readonly="readonly || !edit"
                :required="required"
                :data-errormessage="errormessage"
                :pattern="pattern"
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
                    <i class="fas fa-fw" :class="`fa-${icon}`"></i> {{ buttonText }}
                </button>
                <div class="dropdown-menu">
                    <template v-if="!edit">
                        <button class="dropdown-item" type="button" @click="editSlug">
                            <i class="fas fa-pencil-alt fa-fw"></i> {{ labels.button_edit }}
                        </button>
                    </template>
                    <template v-if="!locked">
                        <button class="dropdown-item" type="button" @click="lockSlug">
                            <i class="fas fa-lock fa-fw"></i> {{ labels.button_locked }}
                        </button>
                    </template>
                    <button class="dropdown-item" type="button" @click="generateSlug">
                        <i class="fas fa-link fa-fw"></i> {{ labels.generate_from }}
                        {{ generate }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import field from '../mixins/value';

export default {
    name: 'EditorSlug',
    mixins: [field],
    props: {
        value: String,
        name: String,
        prefix: String,
        fieldClass: String,
        generate: String,
        labels: Object,
        required: Boolean,
        readonly: Boolean,
        errormessage: String | Boolean, //string if errormessage is set, and false otherwise
        pattern: String | Boolean,
        localize: Boolean,
        isNew: Boolean,
    },
    data() {
        return {
            edit: false,
            locked: true,
            buttonText: this.$props.labels.button_locked,
            icon: 'lock',
        };
    },
    mounted() {
        setTimeout(() => {
            let title = '';
            this.generate.split(',').forEach(element => {
                title = title + document.querySelector(`input[name='fields[${element}]']`).value;
            });
            if (this.shouldGenerateFromTitle(title)) {
                this.icon = 'unlock';
                this.buttonText = this.$props.labels.button_unlocked;
                this.$root.$emit('generate-from-title', true);
                this.generateSlug();
            }
        }, 0);
        this.$root.$on('slugify-from-title', () => this.generateSlug());
    },
    methods: {
        shouldGenerateFromTitle(title) {
            return this.isNew ? true : title.length <= 0 && this.localize;
        },
        editSlug() {
            this.$root.$emit('generate-from-title', false);
            this.edit = true;
            this.locked = false;
            this.buttonText = this.$props.labels.button_edit;
            this.icon = 'pencil-alt';
        },
        lockSlug() {
            this.$root.$emit('generate-from-title', false);
            const slug = this.$options.filters.slugify(this.val);
            this.val = slug;
            this.edit = false;
            this.locked = true;
            this.buttonText = this.$props.labels.button_locked;
            this.icon = 'lock';
        },
        generateSlug() {
            let title = '';
            this.generate.split(',').forEach(element => {
                title = title + ' ' + document.querySelector(`input[name='fields[${element}]']`).value;
            });

            const slug = this.$options.filters.slugify(title);
            this.val = slug;
            this.$root.$emit('generate-from-title', true);

            this.edit = false;
            this.locked = false;
            this.buttonText = this.$props.labels.button_unlocked;
            this.icon = 'unlock';
        },
    },
};
</script>
