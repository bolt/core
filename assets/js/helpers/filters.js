import Vue from 'vue'

Vue.filter('slugify', string => {
  if (string) {
   return string.toString().toLowerCase()
    .replace(/\s+/g, '-')          
    .replace(/[^\w\-]+/g, '')       
    .replace(/\-\-+/g, '-')         
    .replace(/^-+/, '')
    .replace(/-+$/, '');
  }
})