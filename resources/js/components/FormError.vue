<template>

    <li class="p-0 my-0 leading-tight list-none" v-if="!_.isEmpty(errors)">

        <ul v-for="error in _.uniq(errors)" :key="error" class="text-danger p-0 mt-1" v-text="error"></ul>

    </li>

</template>

<script>
    export default {
        props: {
            errors: {
                type: Array,
                required: false,
                default:  ()=>[]
            },
            field: {
                type: Object,
                required: false
            }
        }
    };
</script>

