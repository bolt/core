<template>
  <div class="listing__row--item is-meta">
    <ul class="listing__row--list text-nowrap">
      <li>
        <span class="status mr-2" :class="`is-${record.status}`" :title="record.status"></span
        >{{ record.publishedAt|date }}
      </li>
      <li v-if="size === 'normal'">
        <i class="fas fa-user mr-2"></i> {{ record.authorName }}
      </li>
      <li v-if="size === 'normal'">
        <i class="fas mr-2" :class="record.extras.icon"></i>
        <template v-if="type === 'dashboard'"
          ><a :href="`/bolt/content/${record.contentType}`">
            {{ record.extras.singular_name }}</a
          >
        </template>
        <template v-else>{{ record.extras.singular_name }}</template
        >&nbsp;№ <strong>&nbsp;{{ record.id }}</strong>
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
