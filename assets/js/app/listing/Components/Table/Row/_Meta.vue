<template>
  <div class="listing__row--item is-meta">
    <ul class="listing__row--list">
      <li v-if="size === 'normal'">
        <i class="fas fa-user mr-2"></i> {{ record.authorName }}
      </li>
      <li v-if="size === 'normal'">
        <i class="fas mr-2" :class="record.extras.icon"></i>
        <template v-if="type === 'dashboard'"
          ><a :href="`/bolt/content/${record.contentType}`">
            {{ record.contentType }}</a
          >
        </template>
        <template v-else>{{ record.contentType }}</template
        >&nbsp;№ <strong>&nbsp;{{ record.id }}</strong>
      </li>
      <li>
        <span class="status mr-2" :class="`is-${record.status}`"></span
        >{{ record.publishedAt|date }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'MetaData',
  props: ['type', 'size', 'record'],
};
</script>
