<template>
  <section class="user__theme">
    <h3>Themes:</h3>
    <ul class="user__theme--themes">
      <li v-for="(theme, index) in themes" :key="index" class="theme" @click="selectTheme(theme.value)">
        <h5 class="theme--name">{{theme.name}}</h5>
        <div class="theme--palette">
          <span v-for="(color, index) in theme.colors" :key="index" :style="`background-color: ${color}`"></span>
        </div>
      </li>
    </ul>
  </section>
</template>

<script>

export default {
  name: "user-theme",
  data: () => {
    return {
      themes: [
        {
          name: "Default",
          value: "default",
          colors: ["#1a5597", "#053b79", "#0c223b", "#ffffff"]
        },
        {
          name: "Light",
          value: "light",
          colors: ["#1a5597", "#053b79", "#ffffff", "#f9f9f9"]
        },
      ]
    };
  },
  methods:{
    selectTheme(val){
      const stylesheet = document.querySelector('#theme');
      const url = stylesheet.getAttribute('href').split("theme-")[0];
      const theme = `${url}theme-${val}.css`;
      stylesheet.setAttribute("href", theme);
    },
  }
};
</script>