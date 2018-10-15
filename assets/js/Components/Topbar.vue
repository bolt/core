<template>
        <div class="nav btn-toolbar nav-fill" role="toolbar" aria-label="Toolbar with button groups">
            <span class="topbar-title nav-item" v-html="title">{{ title }}</span>
            <div class="input-group nav-item">
                <div class="input-group-prepend">
                    <div class="input-group-text" id="btnGroupAddon">@</div>
                </div>
                <input type="text" class="form-control" placeholder="Search …" aria-label="Search …" aria-describedby="btnGroupAddon">
            </div>

            <a href="/" class="btn btn-light nav-item">
                <i class="fas fa-external-link-square-alt"></i> &nbsp; View site
            </a>

            <div class="btn-group nav-item" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="text"><i class="fas fa-user-circle"></i> &nbsp; Admin</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1">
                <a href="/bolt/profile-edit" class="dropdown-item">Edit profile</a>
                <a href="/logout" class="dropdown-item">Logout</a>
                </div>
            </div>

        </div>

</template>

<script>
module.exports = {
  data: function() {
    return {
      title: this.$attrs["title"]
    };
  }
};
</script>

<style lang="scss">
.nav.btn-toolbar {
    margin: 0.5rem 1rem 0 3rem;

    .nav-item {
        flex-grow: 0;
        margin-right: 1rem;
    }

    .nav-item.topbar-title {
        font-family: "Source Sans Pro", serif;
        font-size: 22px;
        color: #222;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        flex-grow: 1;
    }
}
</style>
