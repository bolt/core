<template>
    <div>
        <div ref="container">
        </div>
    </div>
</template>

<script>
    import Text from './Text';
    import Vue from 'vue';
    export default {
        name: 'EditorSet',
        props: ['setData', 'id', 'setName'],

        data() {
            return {
                fields: this.setData.fields,
                hash: this.setData.hash
            }
        },
        mounted(){
            console.log(this.fields);
            let thisComponent = this;
            this.fields.forEach(function(field){
                thisComponent.createTextField(field);
            });
        },
        methods: {
            generateFieldName(fieldName){
                return this.setName + '[' + this.hash + '][' + fieldName + ']';
            },
            createTextField(field) {
                const TextClass = Vue.extend(Text);

                const textField = new TextClass({
                    propsData: {
                        value: field._value[0],
                        name: this.generateFieldName(field.name),
                    }
                }).$mount();

                this.$refs.container.appendChild(textField.$el);

            }
        }
    };
</script>
