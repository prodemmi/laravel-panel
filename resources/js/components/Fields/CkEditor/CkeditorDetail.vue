<template>

    <div>

        <div v-bind="data.attributes"
             style="width: 50vw;"
             :class="[ more ? 'hidden' : '' ]"
             v-html="value">
        </div>

        <div v-if="(value && value.length > 40) && more"
             @click="more = false"
             class="relative bottom-0 right-0 cursor-pointer text-blue-400 z-100">Show ...
        </div>

    </div>

</template>

<script>
    export default {
        name: "ckeditor-detail",
        props: ['data', 'value'],
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
