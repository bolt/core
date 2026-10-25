<template>
    <div>
        <div class="input-group mb-2">
            <i class="input-group-text far fa-envelope me-0"></i>
            <input
                :id="id"
                class="form-control"
                :name="name"
                :value="value"
                type="email"
                :required="required"
                :readonly="readonly"
                :data-errormessage="typeof errormessage === 'string' ? errormessage : undefined"
                :pattern="typeof pattern === 'string' ? pattern : undefined"
                :placeholder="typeof placeholder === 'string' ? placeholder : undefined"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    value?: string;
    name?: string;
    id?: string;
    required?: boolean;
    readonly?: boolean;
    errormessage?: string | boolean; //string if errormessage is set, and false otherwise
    pattern?: string | boolean;
    placeholder?: string | boolean;
}>();
</script>
