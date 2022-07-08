<template>

    <div class="inline-flex">
        <lava-search 
            class="w-full ltr:mr-1 rtl:ml-1"
            ref="search"
            :multiple="data.multiple"
            :column="data.column"
            :value="getValue"
            :resource="findResource"
            :placeholder="getlabel"
            :first-search="env !== 'create'"
            :disabled="disabled"
            :clearable="!dialog"
            @on-change="changed"
            uri="api/select-search"/>
            
            <lava-button class="px-2" @click="openPop">Attach</lava-button>
    </div>

</template>

<script>

    import ResourceTable from '../../Table/ResourceTable'

    export default {
        components: {
            ResourceTable
        },
        props: {
            data: Object,
            value: null,
            env: String,
            resource: String,
            disabled: Boolean
        },
        data() {
            return {
                dialog: false
            }
        },
        computed: {
            getValue() {

                var key = this.findResource.modelKey

                return this.data.multiple ? _.map(this.value, key) : _.get(this.value, key, this.value)

            },
            getlabel(){

                var res = this.findResource

                var label = `Search in ${res.route} by ${res.primaryKey}`

                var subtitle = res?.subtitle
                if(!_.isEmpty(subtitle)){
                    label += ` and ${subtitle}`
                }

                return label

            },
            findResource() {

                return this.getResource(this.resource)

            }
        },
        mounted() {

            this.$nextTick(() => {
                var relation = Object.fromEntries(new URLSearchParams(this.$route.query?.relation))
                
                if(this.fullscreen && relation && relation.class === this.resource){

                    delete relation.class

                    this.$refs.search.append(relation)
                    this.dialog = true

                }
            })

        },
        methods: {
            changed(value, column) {

                this.$emit('on-change', {
                    relationType: this.data.relationType,
                    relationModel: this.findResource.model,
                    relationPrimaryKey: this.findResource.primaryKey,
                    update_column: this.data.update_column,
                    column,
                    value
                })

            },
            openPop(){

                var relation = null

                if(this.env === 'edit' && !this.data.relationType.includes('To')){

                    var activeTool = this.activeTool()

                    var vv = _.get(this.$parent.$parent.data, activeTool.primaryKey + '.value')
                    var sb = activeTool.subtitle ? _.get(this.$parent.$parent.data, activeTool.subtitle + '.value') : null

                    var value = {
                        label: sb ? vv + ' - ' + sb : vv,
                        value: vv
                    }
                    console.log(value)

                    relation = new URLSearchParams({
                        class: activeTool.class,
                        ...value
                    }).toString()

                }

                let routeData = this.$router.resolve({name: 'create', params: {resource: this.findResource.route}, query: {fullscreen: true, title: 'Create ' + this.findResource.singularLabel, relation}});
                var win = this.openPopup(routeData.href)

                win.addEventListener('beforeunload', (event) => {
                    var val = $('div#send-data-value')
                    var created_data = JSON.parse(val.html() || '{}')

                    var vv = _.get(created_data, this.findResource.primaryKey)
                    var sb = this.findResource.subtitle ? _.get(created_data, this.findResource.subtitle) : null

                    var value = {
                        label: sb ? vv + ' - ' + sb : vv,
                        value: vv
                    }
                    
                    if(created_data){
                        this.$refs.search.append(value)
                    }

                    val.remove()
                }, false)
                
            }
        }
    }
</script>