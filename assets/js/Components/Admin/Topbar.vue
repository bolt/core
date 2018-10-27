<template>
  <nav class="admin__header--topbar" role="toolbar" aria-label="Toolbar with button groups">
      <h2 class="admin__header--title" v-html="title"></h2>

      <ul class="admin__header--toolbar">
        <li>
          <button class="toolbar--link"> 
              <i class="fas fa-search"></i>
          </button>
        </li>
        <li>
          <a class="toolbar--link" href="/" target="_blank">
              <i class="fas fa-external-link-square-alt"></i>
          </a>
        </li>
        <li>
        <button class="toolbar--link" type="button" data-toggle="dropdown">
          <span class="text"><i class="fas fa-user-circle"></i></span>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a href="/bolt/profile-edit" class="dropdown-item">Edit profile</a>
          <a href="/bolt/logout" class="dropdown-item">Logout</a>
        </div>
        </li>
      </ul>
  </nav>
</template>

<script>
module.exports = {
  name: "admin-topbar",
  props: ["title"]
};
</script>