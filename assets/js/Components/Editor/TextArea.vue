<template>
  <div class="form-group">
      <label>{{ label }}</label>
      <textarea 
        :name="name" 
        :fieldClass="class" 
        v-bind="val"
        cols="25"
      ></textarea>
  </div>
</template>

<script>


export default {
  name: "editor-textarea",
  props: ['value', 'label', 'name', 'fieldClass'],
  mounted(){
    this.val = this.value;
  },
  data: () => {
    return {
      val: null,
    };
  },
};
</script>

<style scoped>
  textarea {
    resize: vertical;
  }
</style>