<template>
    <el-menu mode="horizontal" background-color="transparent">
        <el-menu-item index="1" class="topbar-title">{{ title }}</el-menu-item>
        <el-submenu index="2">
            <template slot="title">Workspace</template>
            <el-menu-item index="2-1">item one</el-menu-item>
            <el-menu-item index="2-2">item two</el-menu-item>
            <el-menu-item index="2-3">item three</el-menu-item>
            <el-submenu index="2-4">
            <template slot="title">item four</template>
            <el-menu-item index="2-4-1">item one</el-menu-item>
            <el-menu-item index="2-4-2">item two</el-menu-item>
            <el-menu-item index="2-4-3">item three</el-menu-item>
            </el-submenu>
        </el-submenu>
        <el-menu-item index="3" disabled>Info</el-menu-item>
        <el-menu-item index="4"><a href="https://www.ele.me" target="_blank">Orders</a></el-menu-item>
    </el-menu>
</template>

<script>
    module.exports = {
        data: function () {
            return {
                title: this.$attrs['title']
            }
        }
    }
</script>

<style>
    .el-menu--horizontal>.topbar-title {
        font-family: 'Source Sans Pro', serif;
        font-size: 22px;
        font-weight: bold;
        color: #222;
        line-height: 57px;
    }
    .el-menu.el-menu--horizontal {
        border-bottom: none;
    }
</style>