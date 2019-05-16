import Vue from 'vue';

Vue.filter('slugify', string => {
  if (string) {
    return string
      .toString()
      .toLowerCase()
      .replace(/\s+/g, '-')
      .replace(/[^\w-]+/g, '')
      .replace(/--+/g, '-')
      .replace(/^-+/, '')
      .replace(/-+$/, '');
  }
});

Vue.filter('strip', string => {
  if (string) {
    return string.replace(/(^")|("$)/g, '');
  }
});

Vue.filter('uppercase', string => {
  if (string) return string.toUpperCase();
});

Vue.filter('trim', (string, length) => {
  if (length == undefined) {
    length = 50;
  }

  return string.length > length ?
      string.substring(0, length - 1) + "…" :
      string;
});
