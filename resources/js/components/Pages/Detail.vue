<template>

    <div class="flex flex-col">

        <i @click="goToBack()" class="ri-arrow-left-line cursor-pointer text-lg"></i>

        <lava-card class="mt-2">

            <template v-slot:header>

                <div class="flex items-center justify-between">

                    <div>{{ activeTool().singularLabel }}: {{ resourceValue(data, activeTool().primaryKey, true) }}
                    </div>

                    <lava-button @click="goToRoute('edit', { id: $route.params.id, resource: $route.params.resource })">
                        Edit
                    </lava-button>

                </div>

            </template>

            <template v-slot:body>

                <div v-for="(field, index) in activeTool().fields"
                     class="flex justify-start w-full px-1 py-2 text-lg border-b-2 border-red-200">

                    <div style="width: 18vw;">{{ field.name }}</div>

                    <component
                            :is="getComponent(field.column, 'detail')"
                            :data="getField(field.column)"
                            :value="resourceValue(data, field.column)"/>

                </div>

            </template>


        </lava-card>

    </div>

</template>

<script>
    export default {
        data() {

            return {
                data: []
            }

        },
        mounted() {

            this.$http.post('/api/detail', {
                resource: this.activeTool().resource,
                search: decodeURIComponent(this.$route.params.id),
                primary_key: this.activeTool().primaryKey
            }).then(res => {

                this.data = res.data

            })

        }
    }
</script>