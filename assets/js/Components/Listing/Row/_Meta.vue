<template>
    <div class="listing__row--item is-meta">
      <ul class="listing__row--list">
          <li v-if="size === 'normal'">
            <i class="fas fa-user mr-2"></i> {{author}}
          </li>
          <li v-if="size === 'normal'">
            <i class="fas mr-2" :class="definition.icon_one"></i> 
            <template v-if="type === 'dashboard'"><a :href="`/bolt/content/${definition.slug}`">{{definition.name}}</a></template>
            <template v-else>{{definition.name}}</template>&nbsp;№ <strong>&nbsp;{{recordId}}</strong>
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
    props: ["type", "size", "recordId", "definition", "date", "status", "author"]
  };
</script>