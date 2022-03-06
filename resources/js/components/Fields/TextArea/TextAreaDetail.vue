<template>

    <div>

        <div v-bind="data.attributes"
             style="width: 50vw;"
             :class="[ more ? 'hidden' : '' ]"
             v-bind.prop="getValue">
        </div>

        <div v-if="(value && value.length > 40) && more"
             @click="more = false"
             class="relative bottom-0 right-0 cursor-pointer text-blue-400 z-100">Show ...
        </div>

    </div>

</template>

<script>

    import {asHtmlMixin} from '../../../mixins'

    export default {
        props: ['data', 'value'],
        mixins: [asHtmlMixin],
        data() {

            return {
                more: false
            }

        },
        mounted() {

            this.more = this.data.more

        }
    }
</script>
