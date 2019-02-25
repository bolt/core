<template>
  <div>
    <div class="input-group">
      <flat-pickr
        v-model="val"
        :config="config"
        class="form-control editor--date"
        placeholder="Select date"
        :name="name"
        :form="form"
        :disabled="disabled == 1"
      >
      </flat-pickr>
      <div class="input-group-append">
        <button
          class="btn btn-secondary"
          :class="{ 'btn-outline-secondary': disabled == 1 }"
          type="button"
          :disabled="disabled == 1"
          data-toggle
        >
          <i class="fa fa-calendar">
            <span aria-hidden="true" class="sr-only">Toggle</span>
          </i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import value from '../../../mixins/value';
import flatPickr from 'vue-flatpickr-component';

export default {
  name: 'EditorDate',

  components: {
    flatPickr,
  },

  mixins: [value],

  props: {
    value: {
      type: String,
      required: false,
      default: '',
    },
    name: {
      type: String,
      required: true,
    },
    disabled: {
      type: String,
      required: true,
    },
    mode: {
      type: String,
      required: true,
      default: 'date',
    },
    form: {
      type: String,
      required: true,
    },
    locale: {
      type: String,
      default: 'en',
    },
  },

  data: () => {
    return {
      config: {
        wrap: true,
        altFormat: 'F j, Y',
        altInput: true,
        dateFormat: 'Z',
        enableTime: false,
      },
    };
  },

  created() {
    if (this.locale !== 'en') {
      const lang = require(`flatpickr/dist/l10n/${this.locale}.js`).default[
        this.locale
      ];
      this.config.locale = lang;
    }
    if (this.mode === 'datetime') {
      this.config.enableTime = true;
      this.config.altFormat = `F j, Y - h:i K`;
    }
  },

  mounted() {},
};
</script>
