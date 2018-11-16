<template>
  <label :for="forlabel">{{label}}</label>
</template>

<script>
  export default {
    name: "editor-label",
    props: ['label', 'forlabel']
  };
</script>