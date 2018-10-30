<template>
  <nav class="admin__header--topbar">
      <h2 class="admin__header--title" v-html="title"></h2>
      <!-- <div class="dropdown-menu dropdown-menu-right">
        <a href="/bolt/profile-edit" class="dropdown-item">Edit profile</a>
        <a href="/bolt/logout" class="dropdown-item">Logout</a>
      </div> -->
  </nav>
</template>

<script>
module.exports = {
  name: "admin-header",
  props: ["title"]
};
</script>