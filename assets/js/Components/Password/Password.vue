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

      <i class="input-group-text fas fa-eye" @click="togglePassword"></i>
    </div>
  </div>
</template>

<script>
export default {
  name: "field-password",
  props: ["value", "name", "id"],
  methods: {
    togglePassword: function(event) {
      let inputElement = event.target.previousElementSibling;
      let inputType = inputElement.attributes.getNamedItem("type").value;

      if (inputType === "password") {
        inputElement.setAttribute("type", "text");
      } else if (inputType === "text") {
        inputElement.setAttribute("type", "password");
      }
    }
  }
};
</script>
