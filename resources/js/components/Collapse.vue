<template>

    <div class="card">

        <h3 class="card--header" v-if="!!$slots.header">

            <div class="flex justify-between">

                <slot name="header"></slot>

                <i class="cursor-pointer"
                   @click="opened = !opened"
                   :class="opened ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>

            </div>

        </h3>

        <div class="card--body" v-show="opened">

            <slot name="body"></slot>

            <div class="card--footer" v-if="!!$slots.footer">

                <slot name="footer"></slot>

            </div>

        </div>

    </div>

</template>

<script>
    export default {
        props: {
            defaultOpened: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                opened: false
            }
        },
        mounted() {

            this.$nextTick(() => {

                this.opened = this.defaultOpened

            })

        }
    }
</script>
