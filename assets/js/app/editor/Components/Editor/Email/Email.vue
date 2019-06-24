<template>
  <div>
    <input
      :id="id"
      type="email"
      :name="name"
      :value="value"
      class="form-control"
    />
  </div>
</template>

<script>
export default {
  name: 'EditorEmail',
  props: ['value', 'name', 'id'],
};
</script>
