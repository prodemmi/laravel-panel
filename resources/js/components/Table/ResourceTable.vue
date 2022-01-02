<template>

    <div v-if="activeTool()"
         class="flex flex-col items-start justify-start wrapper">

        <ActionBar v-if="selected.length"
                   :actions="activeTool().actions"
                   :selected="selected"
                   @on-close="selected = []"
                   @handle-action="handleAction"/>

        <div class="flex items-center my-1">

            <SearchBar :search-in="activeTool().searches" @on-search="search"></SearchBar>

            <lava-button @click="getData(true)" :no-padding="true">

                <i class="ri-refresh-line"></i>

            </lava-button>

            <div class="relative" v-click-outside="hideVisibility">

                <lava-button @click="show_visibility = true"
                             :no-padding="true">

                    <i class="ri-eye-line"></i>

                </lava-button>

                <div v-show="show_visibility"
                     class="absolute z-100 rounded bg-white shadow-md p-2"
                     style="width: max-content;">

                    <div v-for="(column, index) in data.headers" class="flex flex-wrap">

                        <span style="width: 120px;">{{ column.name }}</span>
                        <input v-model="shows[index].show"
                               type="checkbox"
                               :disabled="index <= 2 || column.column === activeTool().primaryKey">

                    </div>

                </div>

            </div>

            <div v-if="activeTool().filters.length" class="relative" v-click-outside="hideFilters">

                <lava-button @click="show_filters = true"
                             :no-padding="true">

                    <i class="ri-filter-2-line"></i>

                </lava-button>

                <div v-show="show_filters"
                     class="absolute z-100 rounded bg-white shadow-md p-2"
                     style="width: max-content;">

                    <div v-for="(filter, i) in activeTool().filters">

                        <div class="flex flex-col justify-start w-full px-1 py-2 text-lg border-b-2 border-red-200">

                            <span v-if="filter.title">{{ filter.title }}: </span>

                            <div class="flex" v-for="(field, index) in filter.fields">

                                <span style="width: 200px;">{{ field.name }}: </span>

                                <component
                                        :key="index"
                                        :is="field.component + '-edit'"
                                        :data="field"
                                        @on-change="doFilter($event, filter, field)"/>

                            </div>

                        </div>

                        <hr v-if="i < activeTool().filters.length - 1">

                    </div>

                </div>

            </div>

        </div>

        <div class="rounded-lg overflow-x-auto" style="min-height: 64vh;width: 100%">

            <div class="relative">

                <transition name="fade">
                    <div v-if="loading" class="resource-table__loading"></div>
                </transition>

                <table class="resource-table">

                    <thead class="bg-primary">

                    <tr>
                        <th class="resource-table__th">

                            <input v-if="activeTool().selectable"
                                   ref="selectAllCheckbox"
                                   type="checkbox"
                                   class="checkbox"
                                   v-model="selectAll">

                        </th>

                        <th v-for="(header, index) in data.headers"
                            v-if="shows[index].show"
                            class="resource-table__th">

                            <div class="flex items-center">

                                <span>{{ header.name }}</span>

                                <span v-if="header.sortable"
                                      @click="setSort(header.column)"
                                      class="cursor-pointer ml-2">

                                    <div v-if="query.sort.column === header.column">
                                        <i class="ri-arrow-up-line" v-if="query.sort.direction === 'DESC'"></i>
                                        <i class="ri-arrow-down-line" v-else-if="query.sort.direction === 'ASC'"></i>
                                    </div>

                                    <i class="ri-arrow-down-s-fill" v-else></i>

                                </span>

                            </div>

                        </th>

                        <th v-if="activeTool().actions.length" class="resource-table__th">
                            Actions
                        </th>

                    </tr>

                    </thead>

                    <tbody class="border-solid border-1 border-gray-300">

                    <tr v-for="(row, index) in data.rows"
                        class="border-solid border-b-1 border-gray-200"
                        :class="_.includes(selected, row) ? 'bg-white' : '' ">

                        <td class="resource-table__td">
                            <input v-if="activeTool().selectable"
                                   type="checkbox"
                                   class="checkbox"
                                   v-model="selected"
                                   :value="row">
                        </td>

                        <td v-for="(header, index) in data.headers"
                            v-if="shows[index].show"
                            class="resource-table__td">

                            <component v-if="getComponent(header.column, 'index')"
                                       :is="getComponent(header.column, 'index')"
                                       :data="getField(header.column)"
                                       :value="resourceValue(row, header.column, false)"/>

                        </td>

                        <th v-if="activeTool().actions.length"
                            :class="selected.length > 0 ? 'pointer-events-none opacity-20' : ''"
                            class="resource-table__td">

                            <div class="flex items-center justify-start text-lg">

                                <lava-tooltip v-for="(action, index) in activeTool().actions" :text="action.name"
                                              :key="index">

                                    <div v-html="action.icon"
                                         @click="handleAction(action, [row])"
                                         style="height: 100%"
                                         :class="action.danger ? 'text-red-600' : 'text-gray-800'"
                                         class="cursor-pointer pr-1 hover:text-gray-400"></div>

                                </lava-tooltip>

                            </div>

                        </th>

                    </tr>

                    </tbody>

                </table>

            </div>

        </div>

        <Pagination :data="data" @change-page="changePage" @change-per-page="changePerPage" :selected="query.per_page"/>

    </div>

</template>

<script>

    import Pagination from './Pagination'
    import ActionBar from '../Table/ActionBar'
    import SearchBar from '../Table/SearchBar'

    export default {
        components: {
            Pagination,
            ActionBar,
            SearchBar
        },
        props: ['resource'],
        data() {
            return {
                data: [],
                selected: [],
                query: {
                    page: 1,
                    per_page: _.first(this.activeTool().perPages),
                    sort: {
                        column: this.activeTool().primaryKey,
                        direction: 'DESC'
                    },
                    filter: []
                },
                show_visibility: false,
                show_filters: false,
                loading: false,
                primaryKey: this.activeTool().primaryKey,
                shows: undefined
            }
        },
        mounted() {

            this.getData()

        },
        watch: {
            'selected'() {

                this.checkIndeterminate()

            }
        },
        computed: {
            selectAll: {
                get: function () {

                    return _.intersectionBy(this.data.rows, this.selected, this.primaryKey).length

                },
                set: function (value) {

                    if (value) {

                        this.selected = _.uniqBy(_.concat(this.selected, this.data.rows), this.primaryKey)
                        return

                    }

                    this.selected = _.differenceBy(this.selected, this.data.rows, this.primaryKey)

                }
            }
        },
        methods: {
            getData(reset = false) {

                if (reset) {

                    this.query.filter = null

                }

                this.setLoading(true)
                this.$http.post('/api/table', {resource: this.resource, query: this.query}).then(res => {

                    this.$nextTick(() => {

                        this.data = res.data
                        this.setLoading(false)
                        this.checkIndeterminate()

                        if (!this.shows) {

                            this.shows = this.data.headers

                        }

                    })

                })

            },
            checkIndeterminate() {

                if (this.$refs.selectAllCheckbox) {

                    if (this.selected < this.data.rows &&
                        _.intersectionBy(this.selected, this.data.rows, this.primaryKey).length) {

                        this.$refs.selectAllCheckbox.indeterminate = true
                        return

                    }

                    this.$refs.selectAllCheckbox.indeterminate = false

                }

            },
            setLoading(status) {

                this.loading = status
                Lava.showLoading(status ? -1 : false)

            },
            setSort(column = null) {

                this.query.sort = {
                    column,
                    direction: this.query.sort.direction === 'DESC' ? 'ASC' : 'DESC'
                }

                this.getData()

            },
            changePage(page) {

                this.query.page = page
                this.getData()
                this.checkIndeterminate()

            },
            changePerPage(event) {

                this.query.page = 1
                this.query.per_page = event.target.value
                this.getData()
                this.checkIndeterminate()

            },
            handleAction(action, row = null) {

                console.log(action)
                if (!row) {

                    row = this.selected

                }

                if (action.danger) {

                    Lava.confirm("sss", "sf").then(() => {

                        this.doAction(action, row)

                    })
                    return

                }

                this.doAction(action, row)

            },
            doAction(action, row) {

                this.$http.post('/api/action',
                    {
                        action: action.action,
                        fields: [],
                        rows: row,
                        resource: this.activeTool().resource
                    }).then(res => {

                    if (res.data.type === 'newWindow') {

                        window.open(res.data.url, res.data.blank ? '_blank' : '_self')
                        return

                    }

                    if (res.data.type === 'route') {

                        this.goToRoute(res.data.name, res.data.params)
                        return

                    }

                    this.selected = []

                    Lava.toast(res.data.message, res.data.type)
                    this.getData()

                })

            },
            doFilter(value, filter, field) {

                let f = _.find(this.query.filter, {filter: filter.filter})

                if (f) {

                    _.set(f, 'fields.' + field.column, value)

                } else {

                    let data = {}

                    data.filter = filter.filter
                    _.set(data, 'fields.' + field.column, value)

                    this.query.filter.push(data)

                }

                this.getData()

            },
            search(search) {

                this.query.search = search
                this.query.page = 1

                this.getData()

            },
            hideVisibility() {

                this.show_visibility = false

            },
            hideFilters() {

                this.show_filters = false

            }
        }
    }

</script>