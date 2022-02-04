<template>

    <resource-table :relationResource="getResource(resource)"
                    :relation="data.relation"
                    :column="data.column"
                    env="detail"
                    :resource="activeTool()"/>

</template>

<script>

    import ResourceTable from '../../Table/ResourceTable'

    export default {
        name: "morph-to-many-detail",
        components: {
            ResourceTable
        },
        props: ['data', 'value', 'resource']
    }

</script>