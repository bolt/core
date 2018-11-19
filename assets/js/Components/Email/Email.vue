<template>
  <div>
    <input
      type="email"
      :id="id"
      :name="name"
      :value="value"
      class="form-control"
    />
  </div>
</template>

<script>
export default {
  name: "field-email",
  props: ["value", "name", "id"]
};
</script>
