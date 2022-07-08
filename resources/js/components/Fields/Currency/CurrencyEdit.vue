<template>

    <div class="flex-center">

        <div class="form-element min-w-element text-center">{{data.currencySymbol || getSymbol(data.locale, data.currency)}}</div>

        <input class="number-input w-full" type="number" v-bind="data.attributes" v-model.trim="model" @input="onChange"/>

    </div>

</template>

<script>

    import {FormMixin} from '../../../mixins'

    export default {
        mixins: [FormMixin],
        props: ['data', 'value'],
        methods: {
            getSymbol(locale, currency){
                return (0).toLocaleString( locale,
                    {
                        style: 'currency',
                        currency: currency,
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }
                ).replace('۰', '').replace('0', '').trim()
            }
        }
    }
</script>