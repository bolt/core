<template>
    <el-card class="box-card">
    <div slot="header" class="clearfix">
        <span>Card name</span>
        <el-button style="float: right; padding: 3px 0" type="text">Operation button</el-button>
    </div>
    <div v-for="newsItem in news" v-if="newsItem" :key="newsItem" class="text item">
        {{ newsItem.title }}
        <div v-html="newsItem.teaser"></div>
    </div>
    </el-card>
</template>

<script>
import DashboardNewsAPI from './service/api/DashboardNews'

export default {
  name: 'DashBoardNews',

  data () {
    return {
      loading: true,
      news: []
    }
  },
  created() {
    DashboardNewsAPI.getNews()
      .then( news => {
        this.news = news
      })
      .catch(error => console.log(error))
      .finally(() => {
        this.loading = false
      })
  }
}
</script>

<style>
  .text {
    font-size: 14px;
  }

  .item {
    margin-bottom: 18px;
  }

  .clearfix:before,
  .clearfix:after {
    display: table;
    content: "";
  }
  .clearfix:after {
    clear: both
  }

  .box-card {
    width: 480px;
  }
</style>