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
            <template v-if="!edit">
              <a class="dropdown-item" @click="editSlug">
                <i class="fas fa-pencil-alt fa-fw"></i> {{ labels.button_edit }}
              </a>
            </template>
            <template v-if="!locked">
              <a class="dropdown-item" @click="lockSlug">
                <i class="fas fa-lock fa-fw"></i> {{ labels.button_locked }}
              </a>
            </template>
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
      locked: false,
      buttonText: 'Locked',
      icon: 'lock',
    };
  },
  mounted() {
    setTimeout(() => {
      console.log('time, time, time');
      let title = '';
      this.generate.split(',').forEach(element => {
        title = title + document.querySelector(`input[name='fields[${element}]']`).value;
      });
      if (title.length <= 0) {
        this.icon = 'unlock';
        this.buttonText = this.$props.labels.button_unlocked;
        this.$root.$emit('generate-from-title', true);
      }
    }, 0);
    this.$root.$on('slugify-from-title', () => this.generateSlug());

  },
  methods: {
    editSlug() {
      this.$root.$emit('generate-from-title', false);
      this.edit = true;
      this.locked = false;
      this.buttonText = this.$props.labels.button_edit;
      this.icon = 'pencil-alt';
    },
    lockSlug() {
      this.$root.$emit('generate-from-title', false);
      const slug = this.$options.filters.slugify(this.val);
      this.val = slug;
      this.edit = false;
      this.locked = true;
      this.buttonText = this.$props.labels.button_locked;
      this.icon = 'lock';
    },
    generateSlug() {
      let title = '';
      this.generate.split(',').forEach(element => {
        title = title + ' ' + document.querySelector(`input[name='fields[${element}]']`).value;
      });

      const slug = this.$options.filters.slugify(title);
      this.val = slug;
      this.$root.$emit('generate-from-title', true);

      this.edit = false;
      this.locked = false;
      this.buttonText = this.$props.labels.button_unlocked;
      this.icon = 'unlock';
    },
  },
};
</script>
