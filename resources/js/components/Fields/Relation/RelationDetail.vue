<template>

    <resource-table :relationResource="getResource(resource)"
                    :relation="data.relationType"
                    :column="data.column"
                    env="detail"
                    :resource="activeTool()"/>
                    
</template>

<script>

    import ResourceTable from '../../Table/ResourceTable'

    export default {
        name: "relation-detail",
        components: {
            ResourceTable
        },
        props: ['data', 'value', 'resource']
    }
</script>