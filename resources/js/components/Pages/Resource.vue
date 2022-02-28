<template>

    <div v-if="resource" class="inline">

        <h2>{{ resource.pluralLabel }}</h2>

        <resource-table :resource="resource" env="index" :key="$route.fullPath"/>

    </div>

</template>

<script>
export default {
    computed: {
        resource(){
            return this.activeTool()
        }
    }
}
</script>