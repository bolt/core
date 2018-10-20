<template>
  <div class="form-group">
    <label>{{ label }}</label>
      <div class="input-group">
        <flat-pickr
          v-model="val"
          :config="config"                                                          
          class="form-control" 
          placeholder="Select date" 
          :name="name"              
          :readonly="readonly"
          :form="form"
          :disabled="readonly == 1"
        >
        </flat-pickr>
        <div class="input-group-append">
          <button 
            class="btn btn-secondary"
            :class="{'btn-outline-secondary': readonly == 1}" 
            type="button"
            :disabled="readonly == 1" 
            data-toggle>
            <i class="fa fa-calendar">
              <span aria-hidden="true" class="sr-only">Toggle</span>
            </i>
          </button>
        </div>
      </div>
  </div>
</template>

<script>
import field from '../../helpers/mixins/fieldValue';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';

export default {
  name: "editor-datetime",
  props: ['value', 'label', 'name', 'readonly', 'form'],
  mixins: [field],
  components: {
      flatPickr
  },
  data: () => {
    return {
      config: {
          wrap: true,
          altFormat: 'Z',
          altInput: true,
          dateFormat: 'Z',
          enableTime: true
      }
    }
  }
};
</script>