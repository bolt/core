<template>
  <div class="input-group">
    <input 
      :name="name" 
      class="form-control"
      placeholder="…" 
      type="number" 
      :step="step" 
      v-model="val"
    >
    <!-- <div class="input-group-append">
      <button 
        class="btn btn-sm btn-secondary" 
        type="button"
        @click="calc(val, '-')"
      ><i class="fas fa-minus"></i></button>
      <button 
        class="btn btn-sm btn-secondary" 
        type="button"
        @click="calc(val, '+')"
      ><i class="fas fa-plus"></i></button>
    </div> -->
  </div>
</template>

<script>
import val from '../../../mixins/value';

export default {
  name: 'editor-number',
  props: ['value', 'label', 'name', 'step', 'type', 'disabled'],
  mixins: [val],

  methods: {
    calc(input, type) {
      let value = input;

      if (input === '') value = 0;
      if (type === '+') this.val = value += this.step;
      if (type === '-') this.val = value -= this.step;
    }
  }
};
</script>
