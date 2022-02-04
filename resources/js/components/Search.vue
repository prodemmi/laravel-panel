<template>

    <VueSelect v-model="model"
               @input="change"
               @search="fetchOptions"
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
        props: ['multiple', 'value', 'uri', 'column', 'resource', 'placeholder'],
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
                this.fetchOptions(this.value)

            });
        },
        methods: {

            change(e) {

                if (this.multiple) {

                    this.model = _.merge(this.model, _.map(e, 'value'))
                    
                } else {
                    
                    this.model = e?.value
                    
                }

                this.$emit("on-change", this.model, this.column);

            },
            fetchOptions: _.debounce(function (search, loading) {

                if (loading) loading(true);

                this.$http
                    .post(this.uri, {
                        resource: this.resource,
                        search: this.init ? this.model : search,
                        init: this.init
                    })
                    .then((res) => {

                        if (this.init) {

                            if(_.isEmpty(this.value)){
                                this.options = res.data
                            }else{
                                this.model = res.data;
                            }
                            this.$emit('on-init')
                        }
                        this.options = _.toArray(res.data);
                        this.temp_options = [...this.options, ...this.temp_options]
                        if (loading) loading(false);
                        this.init = false;

                    });
            }, 400),
        },
    };
</script>
