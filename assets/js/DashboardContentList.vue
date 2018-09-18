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

        <div v-else>
            <h3>Latest {{ type }}</h3>
            <table>
                <tbody>
                    <template v-for="item in content.slice(0, limit)" class="row col">
                        <tr :key="item.id">
                            <td>{{ item.id }}</td>    
                            <td>{{ item.fields[0].value.value }}</td>
                        </tr>
                        <!-- Maybe is better to have a component to print each row? -->
                        <!-- <Context :id="item.id" :key="item.id" :contenttype="content.contenttype" :title="item.fields[0]"></Context> -->
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import Context from './Content';

    export default {
        name: 'context',
        props: [
            'type',
            'limit',
        ],
        components: {
            Context
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
    }
</script>
