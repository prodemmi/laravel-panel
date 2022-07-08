<template>

    <div v-bind="data.attributes">Secret</div>

</template>

<script>

    export default {
        props: ['data', 'value']
    }
    
</script>