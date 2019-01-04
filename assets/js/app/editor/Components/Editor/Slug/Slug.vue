<template>
  <div>
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text">{{ prefix }}</span>
      </div>
      <input
        :name="name"
        placeholder="…"
        type="text"
        class="form-control"
        :class="fieldClass"
        :readonly="!edit"
        v-model="val"
      />
      <div class="input-group-append">
        <button
          type="button"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
          class="btn dropdown-toggle"
          :class="[{ 'btn-primary': edit }, { 'btn-secondary': !edit }]"
        >
          <i class="fas fa-fw" :class="`fa-${icon}`"></i> {{ buttonText }}
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" @click="editSlug">
            <template v-if="!edit">
              <i class="fas fa-pencil-alt fa-fw"></i> Edit
            </template>
            <template v-else>
              <i class="fas fa-lock fa-fw"></i> Lock
            </template>
          </a>
          <a class="dropdown-item" @click="generateSlug();">
            <i class="fas fa-link fa-fw"></i> Generate from: {{ generate }}
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import field from '../../../mixins/value';

export default {
  name: 'editor-slug',
  props: ['value', 'label', 'name', 'prefix', 'fieldClass', 'generate'],
  mixins: [field],
  data: () => {
    return {
      edit: false,
      buttonText: 'Locked',
      icon: 'lock',
    };
  },
  mounted() {
    setTimeout(() => {
      const title = document.querySelector(
        `input[name='fields[${this.generate}]']`
      ).value;
      if (title.length <= 0) {
        this.$root.$emit('generate-from-title', true);
      }
    }, 0);
    this.$root.$on('slugify-from-title', data => this.generateSlug());
  },
  methods: {
    editSlug() {
      this.$root.$emit('generate-from-title', false);
      if (!this.edit) {
        this.edit = true;
        this.buttonText = 'Edit';
        this.icon = 'pencil-alt';
      } else {
        const slug = this.$options.filters.slugify(this.val);
        this.val = slug;
        this.edit = false;
        this.buttonText = 'Locked';
        this.icon = 'lock';
      }
    },
    generateSlug() {
      const title = document.querySelector(
        `input[name='fields[${this.generate}]']`
      ).value;
      const slug = this.$options.filters.slugify(title);
      this.val = slug;
      this.$root.$emit('generate-from-title', true);

      this.edit = false;
      this.buttonText = 'Locked';
      this.icon = 'lock';
    },
  },
};
</script>
