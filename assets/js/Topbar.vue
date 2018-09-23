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
            <a class="item">
                <i class="fas fa-external-link-square-alt"></i> &nbsp; View site
            </a>
            <div class="ui pointing dropdown link item">
                <span class="text"><i class="fas fa-user-circle"></i> &nbsp; Admin</span>
                <i class="dropdown icon"></i>

                <div class="menu">
                    <div class="item">Edit profile</div>
                    <div class="divider"></div>
                    <div class="item">Logout</div>
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
    }
    .ui.attached.menu:not(.tabular) {
        border: 0;
        padding: 0 1rem 0 3rem;
    }
</style>