<template>
  <div>
    <div class="input-group-prepend">
      <input
        type="password"
        :id="id"
        :name="name"
        :value="value"
        class="form-control"
        autocomplete="new-password"
      />

      <i class="input-group-text fas fa-eye"></i>
    </div>
  </div>
</template>

<script>
export default {
  name: "field-password",
  props: ["value", "name", "id"]
};
</script>
