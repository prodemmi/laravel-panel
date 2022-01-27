<template>

    <div class="relative" v-click-outside="hideFilters">

        <lava-button @click="!show_editor ? show_filters = true : null"
                     :color="show_editor? 'info' : 'primary'"
                     :no-padding="true">
            <span v-html="icon('filter-2')"></span>
        </lava-button>

        <div v-show="show_filters"
             class="absolute z-100 rounded bg-white shadow-md p-2"
             style="width: 200px;">

            <ul class="list-none leading-3 m-0 p-0">

                <li v-for="filter in resource.filters"
                    :key="filter.id"
                    class="flex justify-between p-2 hover:bg-gray-200"
                    :class="[ active_filter == filter.title ? 'bg-gray-200 text-gray-800' : '']">

                        <div @click="setFilter(filter)" class="rounded cursor-pointer">{{ filter.title }}</div>
                        <span v-html="icon('delete-bin')" class="text-danger cursor-pointer" @click="deleteFilter(filter)"></span>

                </li>

                <hr v-if="resource.filters.length > 0"/>

                <li class="flex flex-col justify-start w-full p-2 cursor-pointer hover:text-gray-800"
                    @click="show_editor = true;show_filters = false">
                    Create new filter
                </li>

                <li v-if="resource.filters.length > 0 && active_filter"
                    class="flex flex-col justify-start w-full p-2 cursor-pointer hover:text-gray-800"
                    @click="setFilter(null)">
                    Disable filter
                </li>

            </ul>

        </div>

        <div v-if="show_editor"
             class="absolute flex flex-col z-100 rounded bg-white shadow-md p-2"
             style="width: 420px">

            <div v-for="(filter, i) in filters" class="flex justify-start text-lg p-1" :key="i">

                <div class="flex items-start justify-between text-lg w-full">

                    <span style="width: 10vw;">{{ filter.name }}</span>

                    <component class="w-full mr-1"
                               :key="filter.column"
                               :is="filter.component + '-edit'"
                               :value="filter.value"
                               :data="filter"
                               @on-change="changed"/>

                    <span v-html="icon('close')"
                          @click="removeFilter(i)"
                          style="width: 52px;height: 32px;"
                          class="rounded-full bg-primary text-white flex items-center justify-center"></span>

                </div>

            </div>

            <lava-button v-if="!show_fields"
                         @click="show_fields = true"
                         :no-padding="true">
                +
            </lava-button>

            <select v-if="show_fields" class="select" @change="addQuery">

                <option :value="null" selected></option>

                <option v-for="field in getFields(resource.fields)" :value="field.column">

                    {{ field.name }}

                </option>

            </select>

            <div class="flex justify-end mt-2">

                <lava-button v-if="filters.length > 0"
                             @click="createFilter"
                             :no-padding="true">
                    Create
                </lava-button>

                <lava-button @click="cancelFilter"
                             color="danger"
                             :no-padding="true">
                    Cancel
                </lava-button>

            </div>

        </div>

    </div>

</template>

<script>
    export default {
        name: "filters",
        props: ['resource'],
        data() {
            return {
                show_filters: false,
                show_editor: false,
                show_fields: false,
                active_filter: undefined,
                filters: []
            }
        },
        methods: {
            hideFilters() {
                this.show_filters = false;
            },
            hideEditor() {
                this.show_editor = false;
            },
            setFilter(filter) {

                this.hideFilters()
                this.active_filter = filter?.title
                this.$emit('set-filter', filter)

            },
            createFilter() {

                Lava.confirm('Create filter', '', false, {
                    confirmButtonText: 'Create',
                    input: 'text',
                    inputLabel: 'Filter title',
                }).then(res => {

                    if (res.isConfirmed) {

                        this.$http.post('/api/store-filter', {
                            resource: this.resource.resource,
                            filters: this.filters,
                            title: res.value
                        }).then((response) => {

                            if (response.data.result) {

                                Lava.toast(response.data.message, 'success')
                                setTimeout(() => window.location.reload(), 400)

                            }

                        })

                    }

                })

            },
            deleteFilter(filter) {

                Lava.confirm('Delete filter', '', true).then(res => {

                    if (res.isConfirmed) {

                        this.$http.post('/api/delete-filter', {
                            filter_id: filter.id,
                            title: filter.title,
                        }).then((response) => {

                            if (response.data.result) {

                                Lava.toast(response.data.message, 'success')
                                setTimeout(() => window.location.reload(), 400)

                            }

                        })

                    }

                })

            },
            removeFilter(index) {

                this.filters = _.filter(this.filters, (filter, i) => i !== index)

            },
            cancelFilter() {

                this.show_filters = false,
                    this.show_editor = false,
                    this.show_fields = false,
                    this.filters = []

            },
            addQuery(value) {

                this.filters.push(_.find(this.flattenFields(this.resource.fields), {column: value.target.value}))

                this.show_fields = false

            },
            getFields(fields) {

                return _.filter(this.flattenFields(fields), (field) => {

                    return !['password', 'avatar'].includes(field.component) && field.showOnIndex

                })

            },
            changed(value, column) {

                _.find(this.filters, {column}).value = value

            }
        }
    }
</script>

<style scoped>

</style>