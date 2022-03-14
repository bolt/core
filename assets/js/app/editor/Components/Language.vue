<template>
    <div id="multiselect-localeswitcher" class="form-group">
        <label>{{ label }}</label>
        <multiselect
            v-model="locale"
            track-by="name"
            label="localizedname"
            :options="locales"
            :searchable="false"
            :show-labels="false"
            :limit="1"
            @input="switchLocale()"
        >
            <template slot="singleLabel" slot-scope="props">
                <span class="fp me-1" :class="props.option.flag"></span>
                <span>
                    {{ props.option.name }}
                    <small style="white-space: nowrap">({{ props.option.code }})</small>
                </span>
            </template>
            <template slot="option" slot-scope="props">
                <span class="fp me-1" :class="props.option.flag"></span>
                <span>
                    {{ props.option.name }}
                    <small style="white-space: nowrap">({{ props.option.code }})</small>
                </span>
            </template>
        </multiselect>
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect';

export default {
    name: 'EditorLanguage',
    components: { Multiselect },
    props: {
        label: String,
        locales: Array,
        current: String,
    },
    data: () => {
        return {
            locale: {},
        };
    },

    mounted() {
        if (this.current) {
            let current = this.locales.filter(locale => locale.code === this.current);
            if (current.length > 0) {
                this.locale = current[0];
            } else {
                this.locale = this.locales[0];
            }
        } else {
            this.locale = this.locales[0];
        }
    },

    methods: {
        switchLocale() {
            const locale = this.locale.link + location.hash;
            return (window.location.href = locale);
        },
    },
};
</script>
