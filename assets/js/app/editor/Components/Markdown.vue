<template>
    <div>
        <vue-easymde 
            :id="name" 
            v-model="val" 
            :name="name" 
            :configs="config"
        ></vue-easymde>
    </div>
</template>

<script>
import VueEasymde from "vue-easymde";

export default {
    name: 'EditorMarkdown',
    components: {
        VueEasymde
    },
    data(){
        return {
            val: null,
            config: {
                spellChecker: false,
                status: false,
                toggleFullScreen: true,
            },
        }
    },
}
</script>

<style>
    @import "~easymde/dist/easymde.min.css";
</style>