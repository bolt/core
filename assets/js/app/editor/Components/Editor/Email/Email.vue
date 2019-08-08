<template>
  <div>
    <input
      class="form-control"
      :id="id"
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
