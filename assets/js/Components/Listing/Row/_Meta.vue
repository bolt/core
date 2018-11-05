<template>
    <div class="listing__row--item is-meta">
      <ul class="listing__row--list">
          <li v-if="size === 'normal' || size === 'large'">
            <i class="fas fa-user mr-2"></i> {{author}}
          </li>
          <li v-if="size === 'normal' || size === 'large'">
            <i class="fas mr-2" :class="definition.icon_one"></i> {{definition.name}} № <strong>&nbsp;{{recordId}}</strong>
          </li>
          <li>
            <span class="status mr-2" :class="`is-${status}`"></span>{{date.published}}
          </li>
      </ul>
    </div>
</template>

<script>
  export default {
    name: "meta-data",
    props: ["size", "recordId", "definition", "date", "status", "author"]
  };
</script>