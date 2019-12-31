<template>
  <div>
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text">{{ prefix }}</span>
      </div>
      <input
        v-model="val"
        class="form-control"
        :name="name"
        placeholder="…"
        type="text"
        :class="fieldClass"
        :readonly="!edit"
      />
      <div class="input-group-append">
        <button
          type="button"
          class="btn btn-tertiary dropdown-toggle"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
          :class="[{ 'btn-primary': edit }, { 'btn-secondary': !edit }]"
        >
          <i class="fas fa-fw" :class="`fa-${icon}`"></i> {{ buttonText }}
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" @click="editSlug">
            <template v-if="!edit">
              <i class="fas fa-pencil-alt fa-fw"></i> {{ labels.button_edit }}
            </template>
            <template v-else>
              <i class="fas fa-lock fa-fw"></i> {{ labels.button_locked }}
            </template>
          </a>
          <a class="dropdown-item" @click="generateSlug()">
            <i class="fas fa-link fa-fw"></i> {{ labels.generate_from }}
            {{ generate }}
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import field from '../mixins/value';

export default {
  name: 'EditorSlug',
  mixins: [field],
  props: [
    'value',
    'label',
    'name',
    'prefix',
    'fieldClass',
    'generate',
    'labels',
  ],
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
        `input[name='fields[${this.generate}]']`,
      ).value;
      if (title.length <= 0) {
        this.$root.$emit('generate-from-title', true);
      }
    }, 0);
    this.$root.$on('slugify-from-title', () => this.generateSlug());
    this.buttonText = this.$props.labels.button_locked;
  },
  methods: {
    editSlug() {
      this.$root.$emit('generate-from-title', false);
      if (!this.edit) {
        this.edit = true;
        this.buttonText = this.$props.labels.button_edit;
        this.icon = 'pencil-alt';
      } else {
        const slug = this.$options.filters.slugify(this.val);
        this.val = slug;
        this.edit = false;
        this.buttonText = this.$props.labels.button_locked;
        this.icon = 'lock';
      }
    },
    generateSlug() {
      const title = document.querySelector(
        `input[name='fields[${this.generate}]']`,
      ).value;
      const slug = this.$options.filters.slugify(title);
      this.val = slug;
      this.$root.$emit('generate-from-title', true);

      this.edit = false;
      this.buttonText = this.$props.labels.button_locked;
      this.icon = 'lock';
    },
  },
};
</script>
