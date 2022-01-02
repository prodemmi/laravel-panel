<template>

    <div>

        <h2>{{ header }}</h2>

        <resource-table :resource="activeTool()"></resource-table>

    </div>

</template>

<script>

    export default {
        computed: {
            header() {
                return this.activeTool().pluralLabel
            }
        }
    }
</script>