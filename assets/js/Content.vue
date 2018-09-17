<template>
    <div class="card w-100 mt-2">
        <div class="card-body">
            {{ message }}
            Print
        </div>
    </div>
</template>

<script>
    export default {
        name: 'content',
        props: ['message'],
    }
</script>