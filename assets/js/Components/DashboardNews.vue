<template>
<div>
  <div class="ui card" v-for="newsItem in news" v-if="newsItem" :key="newsItem.id">
    <div class="content">
      <div class="header">{{ newsItem.title }}</div>
    </div>
    <div class="content" v-html="newsItem.teaser">
    </div>
  </div>
</div>

</template>

<script>
import DashboardNewsAPI from "../service/api/DashboardNews";

export default {
  name: "DashBoardNews",

  data() {
    return {
      loading: true,
      news: []
    };
  },
  created() {
    this.news = DashboardNewsAPI.getNews();

    // Asynchronously fetch (might take a while)
    setTimeout(() => {
      DashboardNewsAPI.fetchNews()
        .then(news => {
          this.news = news;
        })
        .catch(error => console.log(error))
        .finally(() => {
          this.loading = false;
        });
    }, 200);
  }
};
</script>

<style>
</style>
