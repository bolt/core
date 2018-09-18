<template>
    <tr>
        <td>{{ id }}</td>    
        <td>{{ title.value.value }}</td>
    </tr>
</template>

<script>
    export default {
        name: 'context',
        props: [
            'id',
            'title'
        ],
    }
</script>
