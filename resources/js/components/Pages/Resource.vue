<template>

    <div v-if="resource" class="inline">

        <div class="flex items-center justify-between">
            <h2>{{ resource.pluralLabel }}</h2>

            <lava-button v-if="resource.creatable"
                            @click="goToRoute('create', { resource: resource.route })">
                    Create {{ resource.singularLabel }}
            </lava-button>
        </div>

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