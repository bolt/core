<template>
  <div class="quickedit__row" :class="{'is-large': size === 'large'}">
    <div class="quickedit__row--item is-content">
      <h4>quick edit settings</h4>
    </div>
      <div class="quickedit__row--item is-actions">
        <button @click="cancel()" type="button" class="btn btn-sm btn-secondary" v-if="size !== 'large'">
          <i class="fas fa-ban mr-1"></i>Cancel
        </button>
        <button type="button" class="btn btn-sm btn-success">
          <i class="fas fa-save mr-1"></i>Save
        </button>
      </div>
  </div>
</template>

<script>
  export default {
    name: "quick-editor",
    props: ["size"],
    methods: {
      cancel(){
        this.$emit('quickeditor', false);
      }
    }
  };
</script>