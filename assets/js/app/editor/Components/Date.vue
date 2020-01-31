<template>
  <div>
    <div class="input-group">
      <flat-pickr
        v-model="val"
        class="form-control editor--date"
        :config="config"
        :disabled="disabled"
        :form="form"
        :name="name"
        placeholder="Select date"
        :required="required == 1"
      >
      </flat-pickr>
      <div class="input-group-append">
        <button
          class="btn btn-tertiary"
          :class="{ 'btn-outline-secondary': disabled }"
          type="button"
          :disabled="disabled"
          data-toggle
        >
          <i class="fa fa-calendar">
            <span class="sr-only" aria-hidden="true">{{ labels.toggle }}</span>
          </i>
        </button>
        <button
          class="btn btn-tertiary"
          :class="{ 'btn-outline-secondary': disabled }"
          type="button"
          :disabled="disabled"
          data-clear
        >
          <i class="fa fa-times">
            <span class="sr-only" aria-hidden="true">{{ labels.clear }}</span>
          </i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import value from '../mixins/value';
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
      type: Boolean,
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
    labels: {
      type: String,
      default: '',
    },
    required: {
      type: Number,
      required: true,
    }
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
