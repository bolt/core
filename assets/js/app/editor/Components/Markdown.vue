<template>
    <div>
        <vue-easymde :id="name" v-model="val" :name="name" :configs="config"></vue-easymde>
    </div>
</template>

<script>
import VueEasymde from 'vue-easymde';

export default {
    name: 'EditorMarkdown',
    components: {
        VueEasymde,
    },
    props: {
        value: String,
        name: String,
    },
    data: () => {
        return {
            val: null,
            config: {
                spellChecker: false,
                status: false,
                toggleFullScreen: true,
            },
        };
    },
    mounted() {
        this.val = this.$options.filters.strip(this.value);
    },
};
</script>

<style scoped>
@import '~easymde/dist/easymde.min.css';
</style>
