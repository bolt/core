<template>
    <div>
        <div class="row col">
            <h1>Dashboard</h1>
        </div>

        <div v-if="isLoading" class="row col">
            <p>Loading...</p>
        </div>

        <div v-else-if="hasError" class="row col">
            <div class="alert alert-danger" role="alert">
                {{ error }}
            </div>
        </div>

        <div v-else-if="!hasContent" class="row col">
            No content!
        </div>

        <div v-else v-for="item in content" :key="item.id" class="row col">
            {{ item }}
            <Content :message="item.id"></Content>
        </div>
    </div>
</template>

<script>
    import Content from './Content';

    export default {
        name: 'content',
        props: [
            'type',
            'limit',
        ],
        components: {
            Content
        },
        data () {
            return {
                message: '',
            };
        },
        created () {
            this.$store.dispatch('content/fetchContent', this.type);
        },
        computed: {
            isLoading () {
                return this.$store.getters['content/isLoading'];
            },
            hasError () {
                return this.$store.getters['content/hasError'];
            },
            error () {
                return this.$store.getters['content/error'];
            },
            hasContent () {
                return this.$store.getters['content/hasContent'];
            },
            content () {
                return this.$store.getters['content/content'];
            },
        },
        methods: {
            createContent () {
                this.$store.dispatch('content/createContent', this.$data.message)
                    .then(() => this.$data.message = '')
            },
        },
    }
</script>