<template>

    <div class="relative">

        <lava-button @click="show"
                     v-click-outside="hideFilters"
                     :color="show_editor || is_search || filters.length > 0 ? 'info' : 'primary'"
                     :no-padding="true">
            <span v-html="icon('filter-2')"></span>
        </lava-button>

        <div v-if="show_filters"

             class="absolute z-100 rounded bg-white shadow-md p-2"
             style="width: 200px;">

            <ul class="list-none leading-3 m-0 p-0">

                <li v-for="filter in resource.filters"
                    :key="filter.id"
                    class="flex justify-between p-2 rounded-md hover:bg-gray-200"
                    :class="[ (active_filter && active_filter.title) === filter.title ? 'bg-gray-200 text-gray-800' :
                    '']">

                    <div @click="setFilter(filter)" class="cursor-pointer">{{ filter.title }}</div>

                    <div>

                        <span v-html="icon('edit')"
                              class="cursor-pointer mr-1"
                              @click="editFilter(filter)">
                        </span>

                        <span v-html="icon('delete-bin')"
                              class="text-danger cursor-pointer"
                              @click="deleteFilter(filter)">
                        </span>

                    </div>

                </li>

                <hr v-if="resource.filters.length > 0"/>

                <li class="flex flex-col justify-start w-full p-2 cursor-pointer hover:text-gray-800"
                    @click="show_editor = true;show_filters = false">
                    Custom filter
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
             style="min-width: 500px">

            <div v-for="(filter, i) in filters" class="flex justify-start text-lg p-1" :key="i">

                <div class="flex items-start justify-between text-lg w-full">

                    <span style="width: 25%;min-width: 120px;max-width: 180px;">{{ filter.name }}</span>

                    <component class="w-full mr-1"
                               :key="filter.column"
                               :is="filter.component + '-edit'"
                               :value="filter.value"
                               :data="filter"
                               :disabled="filter.where.where === 'null'"
                               @on-change="changed"/>

                    <span v-text="filter.where.label"
                          @click="changeWhere(filter.component, filter.column)"
                          style="width: 52px;height: 32px;"
                          :class="{ 'bg-danger': filter.where.where === 'null' }"
                          class="rounded-full cursor-pointer bg-primary mr-1 text-white flex items-center justify-center">
                    </span>

                    <span v-html="icon('close')"
                          @click="removeFilter(i)"
                          style="width: 52px;height: 32px;"
                          class="rounded-full cursor-pointer bg-primary text-white flex items-center justify-center">
                    </span>

                </div>

            </div>

            <lava-button v-if="!show_fields"
                         @click="show_fields = true"
                         small
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
                             @click="doFilter(filters)"
                             small
                             color="success"
                             :no-padding="true">
                    Search
                </lava-button>

                <lava-button v-if="filters.length > 0"
                             @click="createFilter"
                             small
                             :no-padding="true">
                    {{ edit_mode ? 'Edit' : 'Create' }}
                </lava-button>

                <lava-button @click="cancelFilter"
                             color="danger"
                             small
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
        computed: {},
        data() {
            return {
                show_filters: false,
                show_editor: false,
                show_fields: false,
                active_filter: undefined,
                filters: [],
                is_search: false,
                edit_mode: false,
                wheres: [
                    {
                        label: '=',
                        where: '='
                    },
                    {
                        label: '>=',
                        where: '>='
                    },
                    {
                        label: '<=',
                        where: '<='
                    },
                    {
                        label: '∅',
                        where: 'null'
                    },
                    {
                        label: 'NQ',
                        where: '<>'
                    },
                    {
                        label: 'L',
                        where: 'like'
                    },
                ]
            }
        },
        methods: {
            show() {

                if (this.is_search && !this.show_editor)
                    this.show_editor = !this.show_editor
                else
                    this.show_filters = !this.show_filters

            },
            hideFilters() {

                this.show_filters = false;

            },
            hideEditor() {

                this.show_editor = false

            },
            setFilter(filter) {
                console.clear();
                console.log("filter ===> ", filter);


                if (filter !== null && (this.active_filter?.id === filter.id)) {
                    return
                }

                if (!filter) {
                    this.is_search = false
                }

                this.active_filter = filter

                this.hideFilters();
                this.$emit('set-filter', filter)

            },
            createFilter() {

                Lava.confirm('Create filter', '', false, {
                    confirmButtonText: this.edit_mode ? 'Edit' : 'Create',
                    input: 'text',
                    inputLabel: 'Filter title',
                    inputValue: this.edit_mode ? this.active_filter.title : undefined
                }).then(res => {

                    if (res.isConfirmed) {

                        this.$http.post('/api/store-filter', {
                            resource: this.resource.resource,
                            filters: this.filters,
                            title: res.value,
                            id: this.active_filter.id,
                            edit: this.edit_mode
                        }).then((response) => {

                            if (response.data.result) {

                                Lava.toast(response.data.message, 'success')
                                this.hideEditor()
                                setTimeout(() => window.location.reload(), 400)

                            }

                        })

                    }

                })

            },
            changeWhere(component, column) {

                let filter = _.find(this.filters, {column})
                let wheres = filter.wheres || this.wheres

                wheres = this.arrayRotate(wheres)

                if (['select', 'badge', 'boolean'].includes(component.toLowerCase())) {

                    filter.wheres = _.filter(wheres, (where) => {
                        return !['<=', '>=', 'like'].includes(where.where)
                    })

                } else if (['id', 'date', 'datetime', 'timezone', 'number', 'currency'].includes(component.toLowerCase())) {

                    filter.wheres = _.filter(wheres, (where) => {
                        return !['like'].includes(where.where)
                    })

                } else if (['text', 'code', 'ckeditor', 'textarea'].includes(component.toLowerCase())) {

                    filter.wheres = _.filter(wheres, (where) => {
                        return !['<=', '>='].includes(where.where)
                    })

                } else {

                    filter.wheres = this.wheres

                }

                filter.where = filter.wheres[0]

            },
            arrayRotate(array) {
                array.push(array.shift());
                return array;
            },
            doFilter(filter) {

                this.is_search = true
                this.hideEditor()
                this.$emit('set-filter', {filter})

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
            editFilter(filter) {

                this.filters = filter.filter
                this.active_filter = filter
                this.edit_mode = true
                this.show_editor = true

            },
            removeFilter(index) {

                this.filters = _.filter(this.filters, (filter, i) => i !== index)

            },
            cancelFilter() {

                this.show_filters = false
                this.show_editor = false
                this.show_fields = false
                this.is_search = false
                this.active_filter = undefined
                this.filters = []

                this.$emit('set-filter', null)

            },
            addQuery(value) {

                const filter = _.find(this.flattenFields(this.resource.fields), {column: value.target.value})
                filter.where = {
                    label: '=',
                    where: '='
                }

                this.filters.push(filter)

                this.show_fields = false

            },
            getFields(fields) {

                return _.filter(this.flattenFields(fields), (field) => {

                    return !['password', 'avatar'].includes(field.component) && field.showOnIndex && !field.relation

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