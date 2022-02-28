<template>

    <VueSelect v-model="model"
               @input="change"
               @search="fetchOptions"
               @search:focus="fetchOptions"
               :options="options"
               :multiple="multiple"
               :push-tags="multiple"
               :placeholder="placeholder"
               :getOptionLabel="getOptionLabel"
               :reduce="(option) => option"
               label="label">

        <slot name="spinner">

            <lava-spinner/>

        </slot>

    </VueSelect>

</template>

<script>
    import VueSelect from "vue-select";
    import "vue-select/dist/vue-select.css";

    export default {
        props: ['multiple', 'value', 'uri', 'column', 'resource', 'placeholder', 'firstSearch'],
        components: {
            VueSelect,
        },
        data() {
            return {
                model: undefined,
                options: [],
                temp_options: [],
                init: true,
                getOptionLabel: (option) => {

                    if (typeof option === 'object') {

                        return option.label

                    }

                    const op = _.find(this.temp_options, { value : option })

                    if(op)
                        return op.label;

                }
            };
        },
        mounted() {

            this.$nextTick(() => {

                this.model = this.value
                
                if(this.firstSearch && this.hasValue){
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

            change(e) {

                if (this.multiple) {

                    this.$emit("on-change", _.map(e, 'value'), this.column)
                    return
                    
                }

                this.$emit("on-change", e?.value, this.column);

            },
            fetchOptions(search, loading) {

                if (loading) loading(true);

                setTimeout(() => {

                    this.$http
                    .post(this.uri, {
                        resource: this.resource.resource,
                        subtitle: this.resource?.subtitle,
                        search: this.init ? this.model : search,
                        init: this.init
                    })
                    .then((res) => {

                        if (this.init && this.firstSearch) {

                            if(this.hasValue){

                                this.model = this.multiple ? res.data : _.first(res.data)

                            }else{

                                this.options = res.data

                            }

                            this.$emit('on-init')

                        }

                        this.options = _.filter( _.toArray(res.data), opp => {

                            return !_.map(this.model, 'value').includes(opp.value)

                        })
                        
                        // this.log(this.options, true)

                        // this.options = _.toArray(res.data)

                        this.temp_options = [...this.options, ...this.temp_options]

                        if (loading) loading(false);
                        
                        this.init = false;

                    });

                }, this.init ? 0 : 400)

            },
        },
    };
</script>
