<template>
  <div class="ui vertical inverted borderless small blue menu">
    <div class="header item logo">
      <h2>Bolt</h2>
    </div>
    <a href="/bolt/" class="active item">
      <span class="fa-stack">
        <i class="fas fa-square fa-stack-2x"></i>
        <i class="fas fa-tachometer-alt fa-stack-1x"></i>
      </span>
     Dashboard
    </a>

    <div class="ui dropdown item">
      <i class="dropdown icon"></i>
      <span class="fa-stack">
        <i class="fas fa-square fa-stack-2x"></i>
        <i class="fas fa-flag fa-stack-1x"></i>
      </span>
      Settings
      <div class="menu">
        <div class="header">Text Size</div>
        <a class="item">Small</a>
        <a class="item">Medium</a>
        <a class="item">Large</a>
      </div>
    </div>
    <a class="item" href="/bolt/users">
      <span class="fa-stack">
        <i class="fas fa-square fa-stack-2x"></i>
        <i class="fas fa-users fa-stack-1x"></i>
      </span>
     Users
    </a>
  </div>
</template>

<style>
.ui.small.vertical.menu {
  border-radius: 0;
  width: auto;
}

.ui.small.vertical.menu .logo {
  color: #FFF;
  background: #24455E;
  text-align: center;
  font-size: 36px;
  margin: 0;
}

.ui.inverted.blue.menu {
    background-color: rgba(0, 0, 0, 0.2);
}

.ui.inverted.blue.menu .active.item {
    background-color: rgba(255, 255, 255, 0.1) !important;
}
</style>

<script>

</script>