<template>

    <div class="flex w-full">
        <VueSelect v-model="model"
               @input="change"
               @search="fetchOptions"
               @search:focus="fetchOptions"
               :options="options"
               :multiple="multiple"
               :push-tags="false"
               :placeholder="placeholder"
               :filterable="!uri"
               :reduce="(option) => option"
               :disabled="disabled"
               :clearable="false"
               :loading="loading"
               ref="search"
               class="w-full"
               :class="{'opacity-70': disabled}"
               label="label">

            <template #open-indicator><span></span></template>

            <template #selected-option="x">
                <span v-if="multiple && openable" class="w-6 cursor-pointer" @click="open(x)"><i class="ri-external-link-line"></i></span>
                <span>{{ x.label }}</span>
            </template>

            <template #option="{ label, subtitle, header, avatar }">

                <div v-if="header" class="p-1 bg-gray-200 text-black hover:bg-primary hover:text-white">
                    <b>{{ label }}</b>
                </div>

                <div class="flex items-center justify-start w-full" v-else>

                    <img v-if="avatar"
                        class="m-2 rounded-full shadow-md h-8 w-8 object-cover"
                        v-lazy="avatar" alt="avatar">
                    
                    <div class="flex flex-col w-full px-2">
                        <span class="text-lg truncate" v-html="label"></span>
                        <span v-if="subtitle" class="text-sm truncate" v-html="subtitle"></span>
                    </div>

                </div>

            </template>

            <template #spinner="{ loading }">

                <div style="width: 36px;height:100%">
                    <lava-spinner v-if="loading" color="primary"></lava-spinner>
                </div>
                
            </template>

        </VueSelect>

        <lava-button v-if="!multiple && openable" class="px-2" @click="open(null)">
            <i class="ri-external-link-line"></i>
        </lava-button>

    </div>

</template>

<script>
    import VueSelect from "vue-select";
    import "vue-select/dist/vue-select.css";
    
    export default {
        props: {
            multiple: {
                default: false
            },
            value: {
                default: null
            },
            uri: {
                required: true
            },
            column: {
                default: null,
                required: false
            },
            placeholder: {
                default: null,
                required: false
            },
            resource: {
                default: null,
                required: false
            },
            firstSearch: {
                type: Boolean,
                default: false,
                required: false
            },
            disabled: {
                type: Boolean,
                default: false,
                required: false
            },
            clearable: {
                default: true
            },
            openable: {
                default: false
            }
        },
        components: {
            VueSelect,
        },
        data() {
            return {
                model: this.multiple ? [] : null,
                options: [],
                loading: false,
                // temp_options: [],
                initilizable: this.firstSearch
            };
        },
        mounted() {

            this.$nextTick(() => {

                this.model = this.value
                
                if(this.initilizable && this.hasValue){
                    this.fetchOptions(this.value)
                }

            });

        },
        computed: {
            hasValue (){
                return _.isArray(this.value) ? !_.isEmpty(this.value) : !(this.value === null || this.value === undefined)
            }
        },
        methods: {
            clear(){
                this.model = null
            },
            open(data){

                var val = data ? data.value : _.first(this.$refs.search.selectedValue).value

                this.goToUrl(`/${this.activePanel()}/resources/${this.resource.route}/${val}/detail`, true)
                this.$refs.search.closeSearchOptions()
                this.$refs.search.mutableLoading = false
            },
            append(value) {

                if (this.multiple) {

                    this.model.push(value)
                    this.change(this.model)
                    return
                    
                }

                this.model = value
                this.change(this.model);

            },
            change(e) {

                if (this.multiple) {

                    this.$emit("on-change", _.map(e, 'value'), this.column)
                    return
                    
                }

                this.$emit("on-change", e?.value, this.column);

            },
            fetchOptions(search, loading = null) {

                // if(!search || search.length === 0 ){
                //     return
                // }

                this.loading = true

                setTimeout(() => {

                    var data = {
                        search: this.initilizable ? this.model : search,
                        init: this.initilizable
                    }

                    if(this.resource){
                        data.resource = this.resource.resource
                        data.subtitle = this.resource?.subtitle
                    }

                    this.$http.post(this.uri, data).then((res) => {

                        if (this.initilizable) {

                            if(this.hasValue){

                                this.model = this.multiple ? res.data : _.first(res.data)

                            }

                            this.$emit('on-init')

                        }
                        
                        if (loading) loading(false);                           
                        
                        this.initilizable = false;

                        this.$nextTick(() => this.options = res.data)

                        setTimeout(() => this.loading = false, 200);

                    });

                }, this.initilizable ? 0 : 400)
            }
        },
    };
</script>
