<template>

    <div>

        <lava-dialog :show="show_dialog"
                     confirmLabel="Attach"
                     :loading="loading"
                     :disabled="data.rules.includes('required') && (search_value === undefined || search_value ===
                     null)"
                     @on-continue="update"
                     @on-init="show_dialog = true"
                     @on-cancel="show_dialog = false"
                     @on-close="show_dialog = false">

            <template v-slot:header>

                Attach {{ _.upperFirst(data.name) }}

            </template>

            <template v-slot:body>

                <div class="flex justify-start py-2">

                    <lava-search class="w-full"
                                 :multiple="false"
                                 :column="data.column"
                                 :value="value"
                                 :resource="findResource"
                                 :placeholder="`Search in ${findResource.route} by ${findResource.primaryKey}`"
                                 @on-change="changed"
                                 uri="api/search-select"/>

                </div>

            </template>

        </lava-dialog>

        <lava-button @click="show_dialog = true" class="mb-2">
            Attach {{ _.upperFirst(data.name) }}
        </lava-button>

        <resource-table :relationResource="getResource(resource)"
                        :relation="data.relation"
                        :column="data.column"
                        env="edit"
                        :resource="activeTool()"/>

    </div>

</template>

<script>

    import ResourceTable from '../../Table/ResourceTable'

    export default {
        name: "has-one-edit",
        components: {
            ResourceTable
        },
        props: ['data', 'value', 'resource'],
        data() {

            return {
                show_dialog: false,
                column: undefined,
                search_value: undefined,
                loading: false
            }

        },
        computed: {
            findResource() {

                return this.getResource(this.resource)

            }
        },
        methods: {
            changed(value, column) {

                this.search_value = value
                this.column = column

            },
            update() {

                this.loading = true

                this.$http.post('/api/update-has-one', {
                    resource: this.activeTool(),
                    search: this.$route.params.primaryKey,
                    update_column: this.column,
                    value: this.search_value
                }).then((res) => {

                    if (res.data) {

                        this.loading = false
                        this.show_dialog = false
                        window.location.reload()

                    }

                }).catch((errors) => {
                    this.loading = false
                    this.show_dialog = false
                })


            }
        }
    }
</script>