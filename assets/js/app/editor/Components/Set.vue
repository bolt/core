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
        name: 'EditorSet',
        props: ['values', 'id'],

        data() {
            let containerFields  = this.values;
            containerFields.forEach(function(field, index, theContainerFieldsArray) {
                theContainerFieldsArray[index] = JSON.parse(field);
            });

            return {
                valuesData: containerFields,
            }
        },
        mounted(){
            this.valuesData.forEach(function(value){
                console.log(value[value.hash + '::title_value']);
            });
        }
    };
</script>
