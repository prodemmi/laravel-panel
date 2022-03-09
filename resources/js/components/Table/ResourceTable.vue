<template>

    <div v-if="relation && data.all === 0 && env !== 'edit'">None</div>

    <div class="flex flex-col items-start justify-start" v-else-if="data.all > 0">

        <lava-dialog :show="selected_action && selected_action.fields.length > 0"
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

        <div v-if="(relation && data.all > 1) || !relation">Total: {{data.total || _.size(data.rows)}}</div>

        <div v-if="!relation" class="flex justify-between items-center w-full my-1">

                        <div class="flex justify-between items-center">
                
                <lava-search-bar :search-in="resource.searches"
                            @on-search="search"/>

                <lava-button @click="getData(true)"
                            :no-padding="true">

                    <i class="ri-refresh-line"></i>

                </lava-button>

                <div class="relative" v-click-outside="hideVisibility">

                    <lava-button @click="show_visibility = true" :no-padding="true">

                        <i class="ri-eye-line"></i>

                    </lava-button>

                    <div v-show="show_visibility"
                        class="absolute z-100 rounded bg-white shadow-md p-2"
                        style="width: max-content">

                        <div v-for="(column, index) in data.headers"
                            :key="index"
                            class="flex flex-wrap">

                            <span style="min-width: 120px">{{ column.name }}</span>

                            <input v-model="shows[index].show"
                                type="checkbox"
                                class="my-0.5"
                                :disabled="(shows[index].show && index <= 2) || column.column === resource.primaryKey"/>

                        </div>

                    </div>

                </div>

                <filters :resource="resource" @set-filter="doFilter"/>
                
            </div>

            <div class="flex">

                <ActionBar  v-if="selected.length"
                    :actions="resource.actions"
                    :selected="selected"
                    :showClose="false"
                    @handle-action="handleAction"/>

                <div v-show="!selected.length" class="relative" v-click-outside="hideActions">

                    <lava-button @click="show_actions = true" :no-padding="true">

                        <i class="ri-router-line"></i>

                    </lava-button>

                    <div v-show="show_actions"
                            class="absolute ltr:right-0 rtl:left-0 z-100 rounded bg-white shadow-md p-2"
                            style="width: max-content">

                        <ActionBar
                            :actions="resource.actions"
                            :selected="null"
                            :showClose="false"
                            @handle-action="handleAction"/>

                    </div>

                </div>

                <div class="relative" v-click-outside="hideExport">

                    <lava-button @click="show_export = true" :no-padding="true">

                        <i class="ri-download-2-line"></i>

                    </lava-button>

                    <div v-show="show_export"
                        class="absolute z-100 ltr:right-0 rtl:left-0 rounded bg-white shadow-md p-2"
                        style="width: max-content">

                        <div v-for="type in ['EXCEL', 'JSON', 'PRINT']" :key="type">
                            <lava-button @click="exportData(type)">{{ type }}</lava-button>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="w-full" v-show="data">

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

                            <template v-for="(header, index) in data.headers">

                                <th :key="index"
                                    v-if="shows[index].show"
                                    class="resource-table__th">

                                    <div class="flex items-center" >

                                        <span>{{ header.name }}</span>

                                        <span v-if="header.sortable"
                                            @click="setSort(header.column)"
                                            class="cursor-pointer ltr:ml-2 rtl:mr-2">

                                            <template v-if="query.sort.column === header.column">

                                                <i v-if="query.sort.direction === 'DESC'"
                                                    class="ri-arrow-up-line">
                                                </i>

                                                <i v-else-if="query.sort.direction === 'ASC'"
                                                      class="ri-arrow-down-line">
                                                </i>

                                            </template>

                                            <i v-else class="ri-arrow-drop-down-line"></i>

                                        </span>

                                    </div>

                                </th>

                            </template>

                            <th v-if="resource.actions.length > 0 && showActions" class="resource-table__th">
                                Actions
                            </th>

                        </tr>

                        </thead>

                        <tbody class="border-solid border-1 border-gray-300">

                        <tr v-for="(row, index) in _.map(data.rows, 'rows')"
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

                            <template v-for="(header, i) in data.headers">

                                <td v-if="shows[i].show"
                                    :key="i"
                                    class="resource-table__td">

                                    <component v-if="getField(!!relation ? relationResource : resource, header.column)"
                                               :is="getField(!!relation ? relationResource : resource, header.column).component +
                                               '-index'"
                                               :data="getField(!!relation ? relationResource : resource, header.column)"
                                               :value="resourceValue(row, header)"/>

                                </td>

                            </template>

                            <th v-if="(!!relation ? relationResource : resource).actions.length && showActions"
                                :class="selected.length > 0 ? 'pointer-events-none opacity-20' : ''"
                                class="resource-table__td">

                                <div class="flex items-center justify-start text-lg">

                                    <lava-tooltip
                                            v-for="action in (!!relation ? relationResource : resource).actions"
                                            :text="_.find(data.rows[index].actions, {name: action.action}).show ? action.name : null"
                                            :key="action.name">

                                        <div v-html="icon(action.icon)"
                                             @click="handleAction(action, [row])"
                                             style="height: 100%"
                                             :class="[`text-${action.color}` , _.find(data.rows[index].actions, {name: action.action}).show ? '' : 'pointer-events-none opacity-30']"
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
    import Filters from "../Table/Filters";
    import NoData from "../Table/NoData";
    import Fields from "../Pages/Fields";

    export default {
        components: {
            Pagination,
            ActionBar,
            Filters,
            NoData,
            Fields
        },
        props: {
            resource: {
                type: Object,
                required: true
            },
            relationResource: {
                type: Object,
                required: false
            },
            relation: {
                type: String,
                required: false
            },
            disablePagination: {
                type: Boolean,
                required: false,
                default: false
            },
            column: {
                type: String,
                required: false
            },
            env: {
                type: String,
                required: false
            },
            showActions: {
                type: Boolean,
                default: true
            }
        },
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
                show_export: false,
                show_actions: false,
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
                    return _.intersectionBy(_.map(this.data.rows, 'rows'), this.selected, this.primaryKey).length;
                },
                set: function (value) {
                    if (value) {
                        this.selected = _.uniqBy(
                            _.concat(this.selected, _.map(this.data.rows, 'rows')),
                            this.primaryKey
                        );
                        return;
                    }

                    this.selected = _.differenceBy(
                        this.selected,
                        _.map(this.data.rows, 'rows'),
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

                    this.updateConfig( () => {

                        this.$http
                            .post("/api/" + (this.relation ? 'relation-table' : 'table'), {
                                resource: this.resource,
                                relation: this.relation,
                                relationResource: this.relationResource?.resource,
                                query: this.query,
                                column: this.column,
                                search: decodeURIComponent(this.$route.params.primaryKey)
                            })
                            .then( (res) =>  {
                                
                                if (!this.shows) {
                                    this.shows = res.data.headers;
                                }

                                this.data = res.data

                                this.checkIndeterminate();

                                this.setLoading(false);

                            })

                    }, this.relation)
                
            },
            checkIndeterminate() {
                if (this.$refs.selectAllCheckbox) {
                    if (
                        this.selected < _.map(this.data.rows, 'rows') &&
                        _.intersectionBy(this.selected, _.map(this.data.rows, 'rows'), this.primaryKey)
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

                if(!this.relation){
                    Lava.showLoading(status ? -1 : false);
                }

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
            changeActionField(val) {

                let data = _.cloneDeep(this.selected_action)

                if (_.isEmpty(data.values)) {
                    data.values = []
                }

                let f = _.find(data.values, {column: val.column})

                if (f) {
                    f.value = val.value
                } else {
                    data.values.push({
                        column: val.column,
                        value: val.value
                    })
                }

                this.selected_action = data

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
            },
            hideExport() {
                this.show_export = false;
            },
            hideActions() {
                this.show_actions = false;
            },
            exportData(type) {

                this.hideExport()
                this.loading = true;

                this.$http
                    .post("/api/export", {
                        resource: this.resource,
                        selected: this.selected,
                        headers: _.filter(this.shows, {show: true}),
                        query: this.query
                    })
                    .then((res) => {

                        this.$nextTick(() => {

                            this.createExport(res.data, type)

                        });

                    }).catch(error => {

                    this.loading = false;

                });

            },
            createExport(data, type) {

                try {

                    var filename = moment().utc().format('MMMM Do YYYY, h:mm:ss a');
                    
                    switch (type) {
                        case 'EXCEL':
                            this.exportExcel(filename, data)
                            break
                        case 'JSON':
                            this.exportJson(filename, data)
                            break
                        case 'PRINT':
                            this.exportPrint(filename, data)
                            break
                    }
                    
                    this.loading = false;

                } catch (e) {

                    console.error(e);
                    Lava.toast(e, 'error', {
                        timer: 10000
                    })
                    this.loading = false

                }

            },
            exportExcel(filename, data) {

                let xlsx = require('json-as-xlsx')

                let exportData = [
                    {
                        sheet: this.resource.pluralLabel,
                        columns: _.map(data.headers, (header, i) => {

                            return {
                                label: header,
                                value: _.find(this.shows, {name: header}).column
                            }

                        }),
                        content: data.data
                    }
                ]

                let settings = {
                    fileName: filename, // Name of the resulting spreadsheet
                    extraLength: 3, // A bigger number means that columns will be wider
                    writeOptions: {} // Style options from https://github.com/SheetJS/sheetjs#writing-options
                }

                xlsx(exportData, settings) // Will download the excel file

            },
            exportJson(filename, data) {
                
                var download = document.createElement('a');

                var file = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data.data));

                download.setAttribute("href", file);
                download.setAttribute("download", `${filename}.json`);
                download.click();

            },
            exportPrint(filename, data) {

                var root = document.createElement('div');

                if(this.$store.getters.getConfig.rtl){

                    root.style.direction = 'rtl'

                }

                var header = document.createElement('h3');
                header.innerHTML = this.resource.pluralLabel

                root.appendChild(header)
                
                var date = document.createElement('h4')
                date.innerHTML = filename

                root.appendChild(date)
                
                const tbl = document.createElement('table');

                tbl.style.width = '100%'
                tbl.style.textAlign = 'left'
                tbl.style.borderCollapse = 'collapse'
                var tr = document.createElement('tr');

                // Create Header
                var thead = document.createElement('thead');
                for (var i = 0; i < data.headers.length; i++) {

                    var th = document.createElement('th');
                    th.style.padding = '8px 4px 8px 4px';
                    th.style.border = '1px solid black';
                    var text = document.createTextNode(data.headers[i]);
                    th.appendChild(text);
                    tr.appendChild(th);

                }
                thead.appendChild(tr)

                tbl.appendChild(thead);

                // Create Rows
                var tbody = document.createElement('tbody');
                for (var i = 0; i < data.data.length; i++) {

                    var tr = document.createElement('tr');

                    for (let j = 0; j < _.values(data.headers).length; j++) {
                        
                        var td = document.createElement('td');
                        td.style.padding = '8px 4px 8px 4px';
                        td.style.border = '1px solid black';
                        var text = document.createTextNode(_.values(data.data[i])[j]);
                        td.appendChild(text);
                        tr.appendChild(td);
                        
                    }

                    tbody.appendChild(tr)

                }

                tbl.appendChild(tbody);

                root.appendChild(tbl)

                var x=window.open();
                    x.document.open();
                    x.document.write(root.outerHTML);
                    x.document.close();

            }

        },
    };

</script>
