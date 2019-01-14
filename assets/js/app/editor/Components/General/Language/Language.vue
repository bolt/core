<template>
  <div class="form-group">
    <label>{{ label }}</label>
    <multiselect
      v-model="locale"
      track-by="name"
      label="localizedname"
      :options="locales"
      :searchable="false"
      :show-labels="false"
      :limit="1"
      @input="switchLocale();"
    >
      <template slot="singleLabel" slot-scope="props">
        <span class="fp mr-1" :class="props.option.flag"></span>
        <span
          >{{ props.option.name }}
          <small style="white-space: nowrap"
            >({{ props.option.code }})</small
          ></span
        >
      </template>
      <template slot="option" slot-scope="props">
        <span class="fp mr-1" :class="props.option.flag"></span>
        <span
          >{{ props.option.name }}
          <small style="white-space: nowrap"
            >({{ props.option.code }})</small
          ></span
        >
      </template>
    </multiselect>
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect';

export default {
  name: 'editor-language',

  components: { Multiselect },

  props: ['label', 'locales', 'current'],

  mounted() {
    const url = new URLSearchParams(window.location.search);

    if (this.current) {
      let current = this.locales.filter(locale => locale.code === this.current);
      if (current.length > 0) {
        this.locale = current[0];
      } else {
        this.locale = this.locales[0];
      }
    } else {
      this.locale = this.locales[0];
    }
  },

  data: () => {
    return {
      locale: {}
    };
  },

  methods: {
    switchLocale() {
      const locale = this.locale.link;
      return (window.location.href = locale);
    }
  }
};
</script>
