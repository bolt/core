<template>
    <div>
        <input
                :id="id"
                class="form-control"
                :name="name"
                :value="values"
                type="email"
        />
    </div>
</template>

<script>
    export default {
        name: 'EditorCollection',
        props: ['values', 'id'],

        mounted(){
            console.log(this.values);
        }
    };
</script>
