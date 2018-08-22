<template>
    <p>{{ greeting }} World!

    <el-row>
        <el-button>Default</el-button>
        <el-button type="primary">Primary</el-button>
        <el-button type="success">Success</el-button>
        <el-button type="info">Info</el-button>
        <el-button type="warning">Warning</el-button>
        <el-button type="danger">Danger</el-button>
    </el-row>

    </p>
</template>

<script>
module.exports = {
    data: function () {
        return {
            greeting: 'Hello'
        }
    }
}
</script>

<style scoped>
p {
    font-size: 2em;
    text-align: center;
}
</style>