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
    export default {
        name: 'EditorCollection',
        props: ['id', 'templates'],
        data(){
            return {
              elements: [],
            }
        },
        methods: {
            addSet(){
              let res = Vue.compile(this.templates.author);
              this.elements.push(res);
            },
        }
    };
</script>
