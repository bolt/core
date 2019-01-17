<template>
    <div class="listing__row--item is-meta">
      <ul class="listing__row--list">
          <li v-if="size === 'normal'">
            <i class="fas fa-user mr-2"></i> {{record.author.displayName}}
          </li>
          <li v-if="size === 'normal'">
            <i class="fas mr-2" :class="meta.icon"></i>
            <template v-if="type === 'dashboard'"><a :href="`/bolt/content/${meta.contentType}`">{{meta.contentType}}</a></template>
            <template v-else>{{meta.contentType}}</template>&nbsp;№ <strong>&nbsp;{{meta.id}}</strong>
          </li>
          <li>
            <span class="status mr-2" :class="`is-${record.status}`"></span>{{record.publishedAt.date|date}}
          </li>
      </ul>
    </div>
</template>

<script>
  export default {
    name: "meta-data",
    props: ["type", "size", "record"]
  };
</script>