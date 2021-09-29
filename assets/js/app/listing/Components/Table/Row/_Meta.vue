<template>
    <div class="listing__row--item is-meta">
        <ul class="listing__row--list">
            <li class="text-nowrap">
                <span class="status" :class="`is-${record.status}`" :title="record.status"></span>
                {{ datetime(record.publishedAt ? record.publishedAt : record.createdAt) }}
            </li>
            <li v-if="size === 'normal'"><i class="fas fa-user"></i> {{ record.authorName }}</li>
            <li v-if="size === 'normal'">
                <i class="fas" :class="record.extras.icon"></i>
                <template v-if="type === 'dashboard'">
                    <a :href="`/bolt/content/${record.contentType}`">{{ record.extras.singular_name }}</a>
                </template>
                <template v-else>{{ record.extras.singular_name }}</template> № {{ record.id }}
            </li>
        </ul>
    </div>
</template>

<script>
import { DateTime } from 'luxon';
export default {
    name: 'MetaData',
    props: {
        type: String,
        size: String,
        record: Object,
    },
    methods: {
        datetime(string) {
            if (string) {
                return DateTime.fromISO(String(string)).toLocaleString();
            }
        }
    }
};
</script>
