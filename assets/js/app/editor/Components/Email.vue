<template>
  <div>
    <input
      :id="id"
      class="form-control"
      :name="name"
      :value="value"
      type="email"
      :required="required == 1"
      :readonly="readonly"
    />
  </div>
</template>

<script>
export default {
  name: 'EditorEmail',
  props: ['value', 'name', 'id', 'required', 'readonly'],
};
</script>
