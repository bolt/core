<template>
    <div class="ui top attached menu">
        <span class="topbar-title" v-html="title">{{ title }}</span>
        <div class="right menu">
            <div class="item">
                <div class="ui icon input">
                    <input type="text" placeholder="Search...">
                    <i class="search link icon"></i>
                </div>
            </div>
            <a href="/" class="item">
                <i class="fas fa-external-link-square-alt"></i> &nbsp; View site
            </a>
            <div class="ui pointing dropdown link item">
                <span class="text"><i class="fas fa-user-circle"></i> &nbsp; Admin</span>
                <i class="dropdown icon"></i>

                <div class="menu">
                    <a href="/bolt/profile-edit" class="item">Edit profile</a>
                    <div class="divider"></div>
                    <a href="/logout" class="item">Logout</a>
                </div>

            </div>
        </div>
    </div>
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
    span.topbar-title {
        font-family: 'Source Sans Pro', serif;
        font-size: 22px;
        color: #222;
        line-height: 52px;
        max-height: 52px;
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        margin-right: 1rem;
    }
    .ui.attached.menu:not(.tabular) {
        border: 0;
        padding: 0 1rem 0 3rem;
        max-width: calc(100% - 4rem);
    }
</style>