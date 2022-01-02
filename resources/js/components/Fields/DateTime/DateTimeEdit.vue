<template>

    <div>
        <flat-pickr :model-value="model" :config="config" name="date"></flat-pickr>
    </div>

</template>

<script>

    import flatPickr from 'vue-flatpickr-component';
    import {Persian} from 'flatpickr/dist/l10n/fa.js';

    export default {
        name: "date-time-detail",
        props: ['data', 'value'],
        data() {

            return {
                model: null,
                config: {
                    wrap: true, // set wrap to true only when using 'input-group'
                    altFormat: 'M j, Y',
                    altInput: true,
                    dateFormat: 'Y-m-d',
                    locale: Persian, // locale for this instance only
                }
            }

        },
        components: {
            flatPickr
        }
    }

</script>