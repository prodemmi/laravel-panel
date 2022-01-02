<template>

    <div class="flex flex-col">

        <i @click="goToBack()" class="ri-arrow-left-line cursor-pointer text-lg"></i>

        <lava-card class="mt-2">

            <template v-slot:header>

                <div>{{ activeTool().singularLabel }}: {{ resourceValue(data, activeTool().primaryKey) }}</div>

            </template>

            <template v-slot:body>

                <div v-for="(field, index) in activeTool().fields"
                     v-if="field.showOnForm"
                     class="flex justify-start w-full px-1 py-2 text-lg border-b-2 border-red-200">

                    <div style="width: 18vw;">
                        <span>{{ field.name }}</span>
                        <span v-if="field.rules.required" class="text-red-600">*</span>
                    </div>

                    <component
                            class="w-full"
                            :is="getComponent(field.column, 'edit')"
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

            this.$http.post('/api/form', {
                resource: this.activeTool().resource,
                search: decodeURIComponent(this.$route.params.id),
                primary_key: this.activeTool().primaryKey
            }).then(res => {

                this.data = res.data

            })

        }
    }
</script>