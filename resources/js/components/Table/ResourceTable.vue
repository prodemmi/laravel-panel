<template>

    <div
            class="flex flex-col items-start justify-start wrapper"
            v-if="data.all > 0">

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

                <div v-if="selected_action.fields" class="flex justify-start py-2" v-for="field in
                selected_action.fields" :key="field.column">

                    <div style="width: 140px">{{ field.name }} <span v-if="field.rules.includes('required')"
                                                                     class="text-danger">*</span></div>

                    <component :is="field.component + '-edit'"
                               :data="field"
                               class="w-full"
                               @on-change="changeActionField"/>


                </div>

            </template>

        </lava-dialog>

        <ActionBar
                v-if="selected.length"
                :actions="resource.actions"
                :selected="selected"
                @on-close="selected = []"
                @handle-action="handleAction"
        />

        <div class="flex justify-between items-center w-full my-1">

            <div class="flex justify-between items-center">

                <SearchBar
                        :search-in="resource.searches"
                        @on-search="search"
                ></SearchBar>

                <!--<lava-button @click="getData(true)" :no-padding="true">-->
                <!--<span v-html="icon('refresh')"></span>-->
                <!--</lava-button>-->

                <div class="relative" v-click-outside="hideVisibility">

                    <lava-button @click="show_visibility = true" :no-padding="true">
                        <span v-html="icon('eye')"></span>
                    </lava-button>

                    <div
                            v-show="show_visibility"
                            class="absolute z-100 rounded bg-white shadow-md p-2"
                            style="width: max-content"
                    >
                        <div
                                v-for="(column, index) in data.headers"
                                :key="index"
                                class="flex flex-wrap"
                        >
                            <span style="width: 220px">{{ column.name }}</span>
                            <input
                                    v-model="shows[index].show"
                                    type="checkbox"
                                    class="my-0.5"
                                    :disabled="index <= 2 || column.column === resource.primaryKey"
                            />
                        </div>
                    </div>
                </div>

                <filters :resource="resource" @set-filter="doFilter"/>

            </div>

            <lava-button @click="goToRoute('create', { resource: resource.route })"
            >Create {{ resource.singularLabel }}
            </lava-button
            >

        </div>

        <div class="w-full">
            <div
                    class="rounded-lg overflow-x-auto whitespace-nowrap	"
                    style="min-height: 64vh; width: 100%"
            >
                <div class="relative">
                    <transition name="fade">
                        <div v-if="loading" class="resource-table__loading"></div>
                    </transition>

                    <table class="resource-table">
                        <thead class="bg-primary">
                        <tr>
                            <th class="resource-table__th">
                                <input
                                        v-if="resource.selectable"
                                        ref="selectAllCheckbox"
                                        type="checkbox"
                                        class="checkbox"
                                        v-model="selectAll"
                                />
                            </th>

                            <th
                                    v-for="(header, index) in data.headers"
                                    :key="index"
                                    v-if="shows[index].show"
                                    class="resource-table__th"
                            >
                                <div class="flex items-center">
                                    <span>{{ header.name }}</span>

                                    <span
                                            v-if="header.sortable"
                                            @click="setSort(header.column)"
                                            class="cursor-pointer ml-2"
                                    >
                      <div v-if="query.sort.column == header.column">
                        <span
                                v-if="query.sort.direction === 'DESC'"
                                v-html="icon('arrow-up')"
                        ></span>
                        <span
                                v-else-if="query.sort.direction === 'ASC'"
                                v-html="icon('arrow-down')"
                        ></span>
                      </div>

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
                        <tr
                                v-for="(row, index) in data.rows"
                                :key="index"
                                class="border-solid border-b-1 border-gray-200"
                                :class="_.includes(selected, row) ? 'bg-white' : ''"
                        >
                            <td class="resource-table__td">
                                <input
                                        v-if="resource.selectable"
                                        type="checkbox"
                                        class="checkbox"
                                        v-model="selected"
                                        :value="row"
                                />
                            </td>

                            <template v-for="(header, index) in data.headers">
                                <td
                                        v-if="shows[index].show"
                                        :key="index"
                                        class="resource-table__td"
                                >
                                    <lava-stack v-if="header.stack" :data="header">
                                        <component
                                                v-for="childHeader in header.headers"
                                                :key="childHeader.column"
                                                :is="childHeader.component + '-index'"
                                                :data="childHeader"
                                                :value="resourceValue(row, childHeader.column, false)"
                                        />
                                    </lava-stack>
                                    <component
                                            v-else-if="getField(resource, header.column)"
                                            :is="
                        getField(resource, header.column).component + '-index'
                      "
                                            :data="getField(resource, header.column)"
                                            :value="resourceValue(row, header.column, false)"
                                    />
                                </td>
                            </template>

                            <th
                                    v-if="resource.actions.length"
                                    :class="
                    selected.length > 0 ? 'pointer-events-none opacity-20' : ''
                  "
                                    class="resource-table__td"
                            >
                                <div class="flex items-center justify-start text-lg">
                                    <lava-tooltip
                                            v-for="(action, index) in resource.actions"
                                            :text="action.name"
                                            :key="index"
                                    >
                                        <div
                                                v-html="action.icon"
                                                @click="handleAction(action, [row])"
                                                style="height: 100%"
                                                :class="action.danger ? 'text-red-600' : 'text-gray-800'"
                                                class="cursor-pointer pr-1 hover:text-gray-400"
                                        ></div>
                                    </lava-tooltip>
                                </div>
                            </th>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <Pagination
                    v-if="data.total > data.per_page"
                    :data="data"
                    @change-page="changePage"
                    @change-per-page="changePerPage"
                    :selected="query.per_page"
            />
        </div>
    </div>

    <no-data :resource="resource" v-else-if="!loading"></no-data>

</template>

<script>
    import Pagination from "./Pagination";
    import ActionBar from "../Table/ActionBar";
    import SearchBar from "../Table/SearchBar";
    import Filters from "../Table/Filters";
    import NoData from "../Table/NoData";

    export default {
        components: {
            Pagination,
            ActionBar,
            SearchBar,
            Filters,
            NoData,
        },
        props: ["resource"],
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
                },
            },
            canDoAction() {

                if (_.isEmpty(this.selected_action?.fields)) {

                    return false

                }

                for (let i = 0; i < this.selected_action.fields.length; i++) {

                    if (this.selected_action.fields[i].rules.includes('required')) {

                        let value = _.find(this.selected_action.values, {
                            column:
                            this.selected_action.fields[i].column
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
                    .post("/api/table", {resource: this.resource, query: this.query})
                    .then((res) => {
                        this.$nextTick(() => {
                            this.data = res.data;
                            this.setLoading(false);
                            this.checkIndeterminate();

                            if (!this.shows) {
                                this.shows = this.data.headers;
                            }
                        });
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
