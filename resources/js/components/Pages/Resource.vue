<template>

    <div v-if="resource" class="resource-page inline">

        <div class="flex items-center justify-between">

            <h2 class="resource-header">{{ resource.pluralLabel }}</h2>

            <lava-button v-if="resource.creatable && !noData"
                         class="resource-create-button"
                         @click="goToRoute('create', { resource: resource.route })">
                    Create {{ resource.singularLabel }}
            </lava-button>

        </div>

        <resource-table :resource="resource" env="index" :key="$route.fullPath" @on-no-data="data => noData = data"/>

    </div>

</template>

<script>
export default {
    data(){
        return {
            noData: false
        }
    },
    computed: {
        resource(){
            return this.activeTool()
        }
    }
}
</script>
