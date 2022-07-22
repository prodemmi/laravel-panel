<template>

    <div v-if="resource" class="resource-page inline">

        <div class="flex items-center justify-between">

            <h2 class="resource-header">{{ resource.pluralLabel }}</h2>

            <lava-button v-if="hasData && resource.creatable"
                         class="resource-create-button"
                         @click="goToRoute('create', { resource: resource.route })">
                    Create {{ resource.singularLabel }}
            </lava-button>

        </div>

        <resource-table :resource="resource" env="index" :key="$route.fullPath" @get-data="data => hasData = data.all > 0"/>

    </div>

</template>

<script>
export default {
    data(){
        return {
            hasData: false
        }
    },
    computed: {
        resource(){
            return this.activeTool()
        }
    }
}
</script>
