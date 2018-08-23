<template>
    <el-card class="box-card">
    <div slot="header" class="clearfix">
        <span>Card name</span>
        <el-button style="float: right; padding: 3px 0" type="text">Operation button</el-button>
    </div>
    <div v-for="o in 4" :key="o" class="text item">
        {{'List item ' + o }}
    </div>
    </el-card>
</template>


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