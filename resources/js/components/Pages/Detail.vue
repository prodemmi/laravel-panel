<template>

    <div class="flex flex-col">

        <lava-dialog :show="selected_action && selected_action.fields.length > 0"
                     :disabled="canDoAction"
                     :danger="selected_action && selected_action.danger"
                     @on-continue="doAction(selected_action, temp_selected_rows)"
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

        <div class="flex items-center justify-between" :class="isMobile ? 'flex-col place-content-end' : ''">

            <div class="flex items-center justify-between">

                <i @click="goToBack()"
                class="cursor-pointer text-lg w-fit" :class="$store.getters.getConfig.rtl ? 'ri-arrow-right-line': 'ri-arrow-left-line'"></i>

                <action-bar 
                    v-if="!_.isEmpty(active_actions)"
                    class="ltr:ml-2 rtl:mr-2"
                    :actions="active_actions"
                    :resource="resource"
                    :selected="[data]"
                    :showClose="false"
                    @handle-action="(action, rows) => {temp_selected_rows = rows; handleAction(action, rows)}"/>
                    
            </div>

            <lava-button v-if="resource.editable"
                         @click="goToRoute('edit', {
                            id: $route.params.id,
                            resource: $route.params.resource
                        })">
                <span class="px-6">Edit</span>
            </lava-button>

        </div>
        <!-- bg-white shadow  -->
        <div v-if="data" class="p-2 text-lg rounded-md my-2">

            <fields :data="data" :fields="resource.fields" :errors="[]" env="detail"/>

        </div>

    </div>

</template>

<script>
    import Fields from './Fields'
    import ActionBar from '../Table/ActionBar'

    export default {
        components: {
            Fields,
            ActionBar
        },
        data() {
            return {
                data: null,
                active_actions: [],
                temp_selected_rows: null,
                selected_action: undefined,
                resource: this.activeTool(),
            };
        },
        mounted() {

            setTimeout(() => this.getData(), 500)

        },
        computed: {
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
            handleAction(action, rows = null, goback = true) {

                if ( !_.isEmpty(action.fields) ) {

                    this.selected_action = action
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
                        action: action || this.selected_action,
                        values: _.flatten(action.values),
                        rows  : rows || this.temp_selected_rows
                    })
                    .then((res) => {

                        Lava.showLoading(false)

                        this.temp_selected_rows     = null
                        this.selected_action = null

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
            async getData(){

                Lava.showLoading(-1)

                await this.$http
                    .post("/api/detail", {
                        resource: this.resource.resource,
                        search: decodeURIComponent(this.$route.params.primaryKey),
                        primary_key: this.resource.primaryKey,
                    })
                    .then((res) => {

                        if(_.isEmpty(res.data)){
                            this.goToBack()
                            return
                        }

                        this.data = res.data.rows;
                    });

                await this.getActiveActions()

                Lava.showLoading(false)

            },
            getActiveActions(){

                return this.$http
                    .post("/api/get-active-actions", {
                        resource: this.resource.resource,
                        search: decodeURIComponent(this.$route.params.primaryKey),
                        primary_key: this.resource.primaryKey,
                    })
                    .then((res) => {
                        this.active_actions = res.data
                    });

            },
            hideDialog() {
                this.selected_action = undefined;
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
            
        }
    };
</script>
