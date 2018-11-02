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
     <ul class="listing--actions">
       <li>
          <a :href="`/bolt/edit/${id}`" class="link">
            <div class="btn-group">
              <button class="btn btn-secondary btn-sm" type="button">
                <i class="far fa-edit mr-1"></i> Edit
              </button>
              <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="sr-only">Toggle Dropdown</span>
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                ...
              </div>
            </div>
          </a>
       </li>
       <li>    
         <a :href="`/bolt/edit/${id}`" class="link"><i class="far fa-caret-square-down mr-1"></i>Quick Edit</a>
        </li>
     </ul>
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