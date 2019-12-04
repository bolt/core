<template>
    <div>
        <div v-for="element in elements">
            <div :is="element"></div>
        </div>

        <button class="btn btn-secondary" type="button" @click="addSet">
            Add set
        </button>
    </div>
</template>

<script>
    import Vue from 'vue';
    var uniqid = require('locutus/php/misc/uniqid');

    export default {
        name: 'EditorCollection',
        props: ['id', 'templates'],
        data() {
            return {
                elements: [],
            }
        },
        methods: {
            addSet() {
                let html = this.templates.author.html.replace(new RegExp(this.templates.author.hash, "g"), uniqid());
                let res = Vue.compile(html);
                this.elements.push(res);
            },
        }
    };
</script>
