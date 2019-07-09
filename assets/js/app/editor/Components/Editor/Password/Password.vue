<template>
  <div>
    <div class="input-group-prepend">
      <input
        :id="id"
        type="password"
        :name="name"
        :value="value"
        class="form-control"
        autocomplete="new-password"
      />

      <i class="input-group-text fas fa-eye" @click="togglePassword" ref="visibilityToggle"></i>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EditorPassword',
  props: ['value', 'name', 'id', 'hidden'],

  methods: {
    togglePassword(event) {
      const iconElement = event.target;
      const inputElement = event.target.previousElementSibling;
      const inputType = inputElement.attributes.getNamedItem('type').value;

      if (inputType === 'password') {
        inputElement.setAttribute('type', 'text');
        iconElement.classList.replace('fa-eye', 'fa-eye-slash');
      } else if (inputType === 'text') {
        inputElement.setAttribute('type', 'password');
        iconElement.classList.replace('fa-eye-slash', 'fa-eye');
      }
    },
  },

  mounted: function() {
    // this.val = this.$options.filters.strip(this.value);
    console.log('joe!', this.value, this.hidden, this.$refs);
    if (!this.hidden) {
      this.$refs.visibilityToggle.click()
    }
  },
};
</script>
