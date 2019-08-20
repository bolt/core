<template>
  <div>
    <input
      :id="id"
      class="form-control"
      :name="name"
      :value="value"
      type="email"
    />
  </div>
</template>

<script>
export default {
  name: 'EditorEmail',
  props: ['value', 'name', 'id'],
};
</script>
