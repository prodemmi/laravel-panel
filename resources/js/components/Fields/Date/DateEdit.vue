<template>

    <div class="flex items-center date-picker">

        <DatePicker
                v-bind="data.attributes"
                class="w-full date-picker"
                v-model="model"
                popover
                color="#212f3c"
                type="date"
                auto-submit
                :format="data.format"
                :display-format="data.jalali ? 'jYYYY/jMM/jDD' : 'YYYY-MM-DD'"
                :locale="data.local"
                @change="onChange"/>

        <i class="ri-close-line cursor-pointer"
           style="margin-left: -30px;"
           v-if="data.nullable && model"
           @click="setNull">
        </i>

    </div>

</template>

<script>

    import DatePicker from 'vue-persian-datetime-picker'

    export default {
        props: ['data', 'value'],
        data() {
            return {
                model: undefined
            }
        },
        components: {
            DatePicker
        },
        mounted() {

            this.$nextTick(() => {

                $(".date-picker label").remove();
                $(".date-picker .vpd-input-group").removeClass();
                $(".date-picker input").addClass('date-picker w-full');
                this.model = this.value

            })

        },
        methods: {
            onChange(event) {

                if (_.isEmpty(event)) {

                    this.$emit('on-change', {
                        column: this.data.column,
                        value: null
                    });
                    return
                }

                this.$emit('on-change', {
                    column: this.data.column,
                    value: event.format('YYYY-MM-DD')
                });

            },
            setNull() {

                this.model = null
                this.onChange(this.model)

            }
        }
    }

</script>