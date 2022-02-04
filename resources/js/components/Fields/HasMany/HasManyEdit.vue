<template>

    <div>

        <lava-dialog :show="show_dialog"
                     confirmLabel="Attach"
                     :loading="loading"
                     :disabled="data.rules.includes('required') && (search_value === undefined || search_value ===
                     null)"
                     @on-continue="update"
                     @on-cancel="show_dialog = false"
                     @on-close="show_dialog = false">

            <template v-slot:header>

                Attach {{ _.upperFirst(findResource.pluralLabel) }}

            </template>

            <template v-slot:body>

                <div class="flex justify-start py-2">

                    <lava-search class="w-full"
                                 :multiple="true"
                                 :column="data.column"
                                 :value="getValue"
                                 :resource="findResource"
                                 :placeholder="`Search in ${findResource.route} by ${findResource.primaryKey}`"
                                 @on-change="changed"
                                 uri="api/search-select"/>

                </div>

            </template>

        </lava-dialog>

        <lava-button @click="show_dialog = true" class="mb-2">
            Attach {{ _.upperFirst(findResource.pluralLabel) }}
        </lava-button>

        <resource-table :relationResource="findResource"
                        :relation="data.relation"
                        :column="data.column"
                        env="edit"
                        :resource="activeTool()"/>

    </div>

</template>

<script>

    import ResourceTable from '../../Table/ResourceTable'

    export default {
        name: "has-many-edit",
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
            getValue() {

                return _.map(this.value, this.findResource.modelKey)

            },
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

                this.$http.post('/api/update-has-many', {
                    model: this.activeTool().model,
                    relationModel: this.findResource.model,
                    primaryKey: this.activeTool().primaryKey,
                    search: this.$route.params.primaryKey,
                    relation: this.column,
                    values: this.search_value
                }).then((res) => {

                    if (res.data) {

                        this.loading = false
                        this.show_dialog = false
                        window.location.reload()

                    }

                }).catch((errors) => {
                    this.loading = false
                })


            }
        }
    }
</script>