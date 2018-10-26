<template>
  <div class="listing__row" :class="`is-${size}`">
    <div 
      class="listing__row--item is-thumbnail" 
      :style="`background-image: url(${thumbnail})`"
      v-if="size === 'normal'"
    ></div>
   <div class="listing__row--item is-details" v-html="excerpt">

   </div>
   <div class="listing__row--item is-meta">
     <ul class="listing__row--list">
        <li v-if="size === 'normal'">
         <i class="fas fa-user mr-2"></i> {{author}}
        </li>
        <li v-if="size === 'normal'">
         <i class="fas mr-2" :class="definition.icon_one"></i> {{definition.name}} № <strong>&nbsp;{{id}}</strong>
        </li>
        <li>
         <span class="status mr-2" :class="`is-${status}`"></span>{{date.published}}
        </li>
     </ul>
   </div>
   <div class="listing__row--item is-actions">
     <button type="button" class="btn btn-primary btn-sm">Small button</button>
     <button type="button" class="btn btn-light btn-sm">Light</button>
   </div>
   <button v-if="sorting" class="listing__row--move"><i class="fas px-2 fa-equals"></i></button>
  </div>
</template>

<script>
module.exports = {
  name: "listing-row",
  props: ["id", "definition", "excerpt", "date", "status", "thumbnail", "author"],
  created(){
    this.$root.$on('listing-row-size', data => this.size = data);
  },
  mounted() {
    this.$root.$on('listing-row-sorting', data => this.sorting = data);
  },
  data: () => {
    return {
      size: "normal",
      sorting: false
    };
  },
};
</script>