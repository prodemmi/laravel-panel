<template>

    <div v-if="relation && data.all === 0 && env !== 'edit'">None</div>

    <div class="flex flex-col items-start justify-start wrapper" v-else-if="data.all > 0">

        <lava-dialog :show="(selected_action && selected_action.fields.length)"
                     :disabled="canDoAction"
                     :danger="selected_action && selected_action.danger"
                     @on-continue="doAction(selected_action)"
                     @on-cancel="hideDialog"
                     @on-close="hideDialog">

            <template v-slot:header>

                {{ selected_action.name }}

            </template>

            <template v-slot:body>

                <div class="flex justify-start py-2">

                    <fields :data="[]"
                            class="w-full"
                            :fields="selected_action.fields"
                            :errors="[]"
                            @on-change="changeActionField"
                            env="edit"/>

                </div>

            </template>

        </lava-dialog>

        <ActionBar v-if="selected.length && !selected_action"
                   :actions="resource.actions"
                   :selected="selected"
                   @on-close="selected = []"
                   @handle-action="handleAction"/>

        <div v-if="!relation" class="flex justify-between items-center w-full my-1">

            <div class="flex justify-between items-center">

                <SearchBar :search-in="resource.searches"
                           @on-search="search"/>

                <lava-button @click="getData(true)"
                             :no-padding="true">

                    <span v-html="icon('refresh')"></span>

                </lava-button>

                <div class="relative" v-click-outside="hideVisibility">

                    <lava-button @click="show_visibility = true" :no-padding="true">

                        <span v-html="icon('eye')"></span>

                    </lava-button>

                    <div v-show="show_visibility"
                         class="absolute z-100 rounded bg-white shadow-md p-2"
                         style="width: max-content">

                        <div v-for="(column, index) in data.headers"
                             :key="index"
                             class="flex flex-wrap">

                            <span>{{ column.name }}</span>

                            <input v-model="shows[index].show"
                                   type="checkbox"
                                   class="my-0.5"
                                   :disabled="index <= 2 || column.column === resource.primaryKey"/>

                        </div>

                    </div>

                </div>

                <filters :resource="resource" @set-filter="doFilter"/>

            </div>

            <lava-button v-if="resource.creatable"
                         @click="goToRoute('create', { resource: resource.route })">
                Create {{ resource.singularLabel }}
            </lava-button>

        </div>

        <div class="w-full">

            <div class="rounded-lg overflow-x-auto whitespace-nowrap"
                 style="width: 100%"
                 :style="{ 'min-height': relation ? 'auto' : '64vh' }">

                <div class="relative">

                    <transition name="fade">

                        <div v-if="loading" class="resource-table__loading"></div>

                    </transition>

                    <table class="resource-table">

                        <thead class="bg-primary">

                        <tr>

                            <th class="resource-table__th">

                                <input v-if="resource.selectable && !relation"
                                       ref="selectAllCheckbox"
                                       type="checkbox"
                                       class="checkbox"
                                       v-model="selectAll"/>
                            </th>

                            <th v-for="(header, index) in data.headers"
                                :key="index"
                                v-if="shows[index].show"
                                class="resource-table__th">

                                <div class="flex items-center">

                                    <span>{{ header.name }}</span>

                                    <span v-if="header.sortable"
                                          @click="setSort(header.column)"
                                          class="cursor-pointer ml-2">

                                          <template v-if="query.sort.column === header.column">

                                            <span v-if="query.sort.direction === 'DESC'"
                                                  v-html="icon('arrow-up')">
                                            </span>

                                            <span v-else-if="query.sort.direction === 'ASC'"
                                                  v-html="icon('arrow-down')">
                                            </span>

                                          </template>

                                        <span v-else v-html="icon('arrow-drop-down')"></span>

                                    </span>

                                </div>

                            </th>

                            <th v-if="resource.actions.length" class="resource-table__th">
                                Actions
                            </th>

                        </tr>

                        </thead>

                        <tbody class="border-solid border-1 border-gray-300">

                        <tr v-for="(row, index) in data.rows"
                            :key="index"
                            class="border-solid border-b-1 border-gray-200"
                            :class="_.includes(selected, row) ? 'bg-white' : ''">

                            <td class="resource-table__td">

                                <input v-if="resource.selectable && !relation"
                                       type="checkbox"
                                       class="checkbox"
                                       v-model="selected"
                                       :value="row"/>

                            </td>

                            <template v-for="(header, index) in data.headers">

                                <td v-if="shows[index].show"
                                    :key="index"
                                    class="resource-table__td">

                                    <component v-if="getField(!!relation ? relationResource : resource, header.column)"
                                               :is="getField(!!relation ? relationResource : resource, header.column).component +
                                               '-index'"
                                               :data="getField(!!relation ? relationResource : resource, header.column)"
                                               :value="resourceValue(row, header)"/>

                                </td>

                            </template>

                            <th v-if="(!!relation ? relationResource : resource).actions.length"
                                :class="selected.length > 0 ? 'pointer-events-none opacity-20' : ''"
                                class="resource-table__td">

                                <div class="flex items-center justify-start text-lg">

                                    <lava-tooltip
                                            v-for="(action, index) in (!!relation ? relationResource : resource).actions"
                                            :text="action.name"
                                            :key="index">

                                        <div v-html="action.icon"
                                             @click="handleAction(action, [row])"
                                             style="height: 100%"
                                             :class="action.danger ? 'text-red-600' : 'text-gray-800'"
                                             class="cursor-pointer pr-1 hover:text-gray-400">
                                        </div>

                                    </lava-tooltip>

                                </div>

                            </th>

                        </tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <pagination v-if="(data.all > data.per_page) && !this.disablePagination"
                        :data="data"
                        @change-page="changePage"
                        @change-per-page="changePerPage"
                        :selected="query.per_page"/>

        </div>

    </div>

    <no-data v-else-if="!relation && !loading" :resource="resource"></no-data>

</template>

<script>

    import Pagination from "./Pagination";
    import ActionBar from "../Table/ActionBar";
    import SearchBar from "../Table/SearchBar";
    import Filters from "../Table/Filters";
    import NoData from "../Table/NoData";
    import Fields from "../Pages/Fields";

    export default {
        components: {
            Pagination,
            ActionBar,
            SearchBar,
            Filters,
            NoData,
            Fields
        },
        props: ['resource', 'relationResource', 'relation', 'disable-pagination', 'column', 'env'],
        data() {
            return {
                data: [],
                selected: [],
                temp_selected: [],
                query: {
                    page: 1,
                    per_page: _.first(this.resource.perPages),
                    sort: {
                        column: this.resource.sort[0],
                        direction: this.resource.sort[1],
                    }
                },
                show_visibility: false,
                loading: false,
                selected_action: undefined,
                primaryKey: this.resource.primaryKey,
                shows: undefined
            };
        },
        mounted() {

            this.getData();

        },
        watch: {
            selected() {
                this.checkIndeterminate();
            },
        },
        computed: {
            selectAll: {
                get: function () {
                    return _.intersectionBy(this.data.rows, this.selected, this.primaryKey)
                        .length;
                },
                set: function (value) {
                    if (value) {
                        this.selected = _.uniqBy(
                            _.concat(this.selected, this.data.rows),
                            this.primaryKey
                        );
                        return;
                    }

                    this.selected = _.differenceBy(
                        this.selected,
                        this.data.rows,
                        this.primaryKey
                    );
                }
            },
            canDoAction() {

                if (_.isEmpty(this.selected_action?.fields)) {

                    return false

                }

                let newFields = this.flattenFields(this.selected_action.fields)

                for (let i = 0; i < newFields.length; i++) {

                    if (newFields[i].rules.includes('required')) {

                        let value = _.find(this.selected_action.values, {
                            column:
                            newFields[i].column
                        })?.value

                        if (value === undefined || value === null) {
                            return true
                        }

                    }

                }

                return false

            }
        },
        methods: {
            hideDialog() {
                this.selected_action = undefined;
            },
            getData(reset = false) {

                if (reset) {
                    this.query.filter = null;
                }

                this.setLoading(true);
                this.$http
                    .post("/api/" + (this.relation ? 'relation' : 'table'), {
                        resource: this.resource,
                        relation: this.relation,
                        relationResource: this.relationResource?.resource,
                        query: this.query,
                        column: this.column,
                        search: decodeURIComponent(this.$route.params.primaryKey)
                    })
                    .then((res) => {
                        this.$nextTick(() => {
                            this.data = res.data;
                            this.setLoading(false);
                            this.checkIndeterminate();

                            if (!this.shows) {
                                this.shows = this.data.headers;
                            }
                        });
                    }).catch((res) => {

                    this.setLoading(false);

                });
            },
            checkIndeterminate() {
                if (this.$refs.selectAllCheckbox) {
                    if (
                        this.selected < this.data.rows &&
                        _.intersectionBy(this.selected, this.data.rows, this.primaryKey)
                            .length
                    ) {
                        this.$refs.selectAllCheckbox.indeterminate = true;
                        return;
                    }

                    this.$refs.selectAllCheckbox.indeterminate = false;
                }
            },
            setLoading(status) {
                this.loading = status;
                Lava.showLoading(status ? -1 : false);
            },
            setSort(column = null) {
                this.query.sort = {
                    column,
                    direction: this.query.sort.direction === "DESC" ? "ASC" : "DESC",
                };

                this.getData();
            },
            changePage(page) {
                this.query.page = page;
                this.getData();
                this.checkIndeterminate();
            },
            changePerPage(event) {
                this.query.page = 1;
                this.query.per_page = event.target.value;
                this.getData();
                this.checkIndeterminate();
            },
            changeActionField(value, column) {

                let data = _.cloneDeep(this.selected_action)

                if (_.isEmpty(data.values)) {
                    data.values = []
                }

                let f = _.find(data.values, {column})

                if (f) {
                    f.value = value
                } else {
                    data.values.push({
                        column,
                        value
                    })
                }

                this.selected_action = data

                console.log(this.selected_action.values)

            },
            doFilter(filter) {

                this.query.filter = filter
                this.getData();

            },
            search(search) {
                this.query.search = search;
                this.query.page = 1;

                this.getData();
            },
            hideVisibility() {
                this.show_visibility = false;
            }
        },
    };

</script>
