<template>

    <div v-if="relation && data.all === 0 && env !== 'edit'">None</div>

    <no-data v-else-if="!relation && !loading && data.all === 0" :resource="resource"/>

    <div v-else-if="data.all" class="flex flex-col items-start justify-start" >

        <lava-dialog :show="selected_action_dialog"
                     :disabled="canDoAction"
                     :danger="selected_action_dialog && selected_action_dialog.danger"
                     @on-continue="doAction(selected_action_dialog, temp_selected_rows)"
                     @on-cancel="hideDialog"
                     @on-close="hideDialog">

            <template v-slot:header>

                {{ selected_action_dialog.name }}

            </template>

            <template v-slot:body>

                <div class="flex justify-start py-2">

                    <fields :data="[]"
                            class="w-full"
                            :fields="selected_action_dialog.fields"
                            :errors="[]"
                            @on-change="changeActionField"
                            env="edit"/>

                </div>

            </template>

        </lava-dialog>

        <div v-if="(relation && data.all > 1) || !relation" class="flex-center mb-1">
            Total
            :
            {{ data.total || _.size(data.rows) }}  

            <span v-if="query.limit > 0" class="mx-2">
            (Limit {{ query.limit }})
            </span>
        </div>

        <!-- Options -->

        <div v-if="!relation" class="flex justify-between items-center w-full my-1">

            <div class="flex justify-between items-center mb-2">

                <lava-search-bar class="ltr:mr-1 rtl:ml-1" :search-in="resource.searches" @on-search="search"/>

                <columns-option class="mx-1" :resource="resource" :headers="data.headers" :shows="shows"
                                @on-change="value => shows = value"/>

                <filters-option class="mx-1" :resource="resource" @set-filter="doFilter" @on-limit="limit"/>

                <export-option class="mx-1" :resource="resource" @on-export="type => exportData(type)"/>

            </div>

            <actions-option class="mx-1" :selected="selected" :actions="resource.actions" @handle-action="(action, rows) => {temp_selected_rows = rows;handleAction(action, rows)}"/>

        </div>

        <div class="w-full" v-show="data">

            <div class="rounded-lg overflow-x-auto whitespace-nowrap"
                 style="width: 100%"
                 :style="{ 'min-height': relation ? 'auto' : '62vh' }">

                <div class="relative">

                    <transition name="fade">

                        <div v-show="false" class="resource-table__loading"></div>

                    </transition>

                    <table class="resource-table" :class="{'black-overlay': loading}">

                        <thead class="bg-primary select-none">

                            <table-head :headers="data.headers" :resource="resource" :show-actions="showActions" :query="query" :shows="shows" :relation="relation" @on-sort="column => setSort(column)"> 
                                <th class="resource-table__th">

                                    <input v-if="resource.selectable && !relation"
                                        ref="selectAllCheckbox"
                                        type="checkbox"
                                        class="checkbox"
                                        v-model="selectAll"/>

                                </th>
                            </table-head>

                        </thead>

                        <tbody class="border-solid border-1 border-gray-300">

                        <tr v-for="(row, index) in data.rows"
                            :key="index"
                            class="border-solid border-b-1 border-gray-200"
                            :class="_.includes(selected, row.rows) ? 'bg-white' : ''">

                            <td class="resource-table__td" style="width: 16px">

                                <input v-if="resource.selectable && !relation"
                                    type="checkbox"
                                    class="checkbox"
                                    v-model="selected"
                                    :value="row.rows"/>

                            </td>

                            <table-row v-for="(header, i) in data.headers" :key="header.column" :index="i" :show="shows[i].show" :resource="resource" :original-resource="getOriginalResource" :data="data" :header="header" :row="row"/>

                            <table-actions v-if="showActions" :class="{'opacity-50 pointer-events-none': selected.length}" :row="row" :relation="relation" :actions="getOriginalResource.actions" @handle-action="(action, row) => {temp_selected_rows = row ; handleAction(action, row)}" />

                        </tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <pagination v-if="(data.all > data.per_page) && paginate"
                        :data="data"
                        @change-page="changePage"
                        @change-per-page="changePerPage"
                        :selected="query.per_page"/>

        </div>

    </div>

</template>

<script>

import Pagination    from "./Pagination";
import ActionBar     from "../Table/ActionBar";
import FiltersOption from "../Table/FiltersOption";
import ColumnsOption from "../Table/ColumnsOption";
import ActionsOption from "../Table/ActionsOption";
import ExportOption  from "../Table/ExportOption";
import TableActions  from "../Table/TableActions";
import TableHead  from "../Table/TableHead";
import TableRow  from "../Table/TableRow";
import NoData        from "../Table/NoData";
import Fields        from "../Pages/Fields";

export default {
    components: {
        Pagination,
        ActionBar,
        FiltersOption,
        ColumnsOption,
        ActionsOption,
        ExportOption,
        TableActions,
        TableHead,
        TableRow,
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
        paginate: {
            type: Boolean,
            default: true
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
            loading: true,
            primaryKey: this.resource.primaryKey,
            selected_action_dialog: null,
            temp_selected_rows: null,
            shows: undefined,
            getOriginalResource: !!this.relation ? this.relationResource : this.resource
        };
    },
    mounted() {

        this.query.limit = !!this.relation ? null : this.resource.limit

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
            console.log('asdasd')

            if (!this.selected_action_dialog) {

                return false

            }

            let newFields = this.flattenFields(this.selected_action_dialog.fields)

            for (let i = 0; i < newFields.length; i++) {

                if (newFields[i].rules.includes('required')) {

                    let value = _.find(this.selected_action_dialog.values, {
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
        limit: _.debounce(function (value){

            this.query.limit = value
            this.getData()

        }, 400),
        handleAction(action, rows = null, goback = false) {

            if ( !_.isEmpty(action.fields) ) {

                this.selected_action_dialog = action
                return

            }

            this.doAction(action, rows, goback)

        },
        doAction(action = null, rows = null, goback = false){

            if ( action.danger ) {
                Lava.confirm(action.name, action.help, action.danger).then(res => {
                    if ( res.isConfirmed ) this.action(action, rows, goback)
                });
                return
            }

            this.action(action, rows, goback)

        },
        action(action = null, rows = null, goback = false) {

            Lava.showLoading(-1)

            return this.$http
                .post("/api/action", {
                    action: action || this.selected_action_dialog,
                    values: _.flatten(action.values),
                    rows  : rows || this.temp_selected_rows
                })
                .then((res) => {

                    Lava.showLoading(false)

                    this.selected = []
                    this.temp_selected_rows     = null
                    this.selected_action_dialog = null

                    if ( this.goback ) {
                        this.goToBack()
                        return
                    }

                    if ( res.data.type === "newWindow" ) {
                        window.open(res.data.url, res.data.blank ? "_blank" : "_self");
                        return;
                    }

                    if ( res.data.type === "dialog" ) {
                        Lava.confirm(res.data.title, res.data.view, false, {
                            showCancelButton : false,
                            confirmButtonText: 'Ok', ...res.data.options
                        })
                        return;
                    }


                    if ( res.data.type === "route" ) {
                        this.goToRoute(res.data.name, res.data.params);
                        return;
                    }

                    Lava.toast(res.data.message, res.data.type)
                    this.updateConfig(this.getData())

                });
        },
        hideDialog() {
            this.selected_action_dialog = undefined;
        },
        getData(reset = false) {

            if (reset) {
                this.query.filter = null;
            }

            this.setLoading(true);

            this.updateConfig(() => {

                this.$http
                    .post("/api/" + (this.relation ? 'relation-table' : 'table'), {
                        resource: this.resource,
                        relation: this.relation,
                        relationResource: this.relationResource?.resource,
                        query: this.query,
                        column: this.column,
                        paginate: this.paginate,
                        search: decodeURIComponent(this.$route.params.primaryKey)
                    })
                    .then((res) => {

                        this.data = res.data

                        this.$emit('get-data', this.data)

                        if (!this.shows) {
                            this.shows = res.data.headers;
                        }

                        this.checkIndeterminate();

                        this.setLoading(false)

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

            if (!this.relation) {
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

            let values = this.selected_action_dialog?.values || []

            let f = _.find(values, {column: val.column})

            if (f) {

                f.value = val.value

            } else {
                values.unshift(val)
            }

            if(val.value === null){
                values = _.without(values, {column: val.column})
            }

            // this.selected_action_dialog = data
            this.$set(this.selected_action_dialog, 'values', values)
            console.log(this.selected_action_dialog)

        },
        doFilter(filter) {

            if (filter || (!filter && this.query.filter)) {

                this.getData();

            }

            this.query.filter = filter

        },
        search(search) {
            this.query.search = search;
            this.query.page = 1;

            this.getData();
        },
        hideVisibility() {
            this.show_visibility = false;
        },
        exportData(type) {

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

            if (this.$store.getters.getConfig.rtl) {

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

            var x = window.open();
            x.document.open();
            x.document.write(root.outerHTML);
            x.document.close();

        }

    },
};

</script>
