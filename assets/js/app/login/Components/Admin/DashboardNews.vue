<template>
  <div>
    <div v-for="newsItem in news" :key="newsItem.id" class="card card mb-4">
      <div class="card-header">{{ newsItem.title }}</div>
      <!-- eslint-disable-next-line vue/no-v-html -->
      <div class="card-body" v-html="newsItem.teaser"></div>
    </div>
  </div>
</template>

<script>
import { fetchNews, getNews } from '../../../../services/api/dashboardNews';

export default {
  name: 'DashBoardNews',

  data() {
    return {
      loading: true,
      news: [],
    };
  },
  created() {
    this.news = getNews();

    // Asynchronously fetch (might take a while)
    setTimeout(() => {
      fetchNews()
        .then(news => {
          this.news = news;
        })
        .catch(error => console.log(error))
        .finally(() => {
          this.loading = false;
        });
    }, 200);
  },
};
</script>

<style></style>
