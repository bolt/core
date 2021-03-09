<template>
    <transition name="card">
        <iframe name="preview_area"></iframe>
    </transition>
</template>

<script>
export default {
    name: 'LivePreview',
    mounted() {
        $(document.getElementById('editcontent').elements).change(function(){
            // Submit the live preview
            $("#live_preview").click();
        })
    },
}
</script>

<style scoped>
iframe[name="preview_area"] {
    width: 100%;
    height: 100%;
    min-height: 70vh;
    border: none;
}
</style>
