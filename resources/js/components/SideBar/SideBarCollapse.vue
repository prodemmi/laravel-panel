<template>

    <div class="side-bar-collapse">

        <h4 class="side-bar-collapse--header" v-if="!!$slots.header" @click="Toggle()">

            <div class="flex items-center justify-between p-1">

                <slot name="header"></slot>

                <i :class="isOpened ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>

            </div>

        </h4>

        <div class="side-bar-collapse--body overflow-hidden transition-all" :style="{'height': calcedHeight}" :id="id">

            <slot name="body"></slot>

        </div>

    </div>

</template>

<script>
    export default {
        props: {
            defaultOpened: {
                type: Boolean,
                default: false
            },
            parentOpened: {
                type: Boolean,
                default: undefined
            },
            index: {
                type: String
            }
        },
        data() {
            return {
                opened: true,
                firstHeight: 0,
                id: Math.random().toString(36).slice(2)
            }
        },
        computed: {
            calcedHeight(){

                if(this.firstHeight > 0){

                    return (this.isOpened ? this.firstHeight : 0) + 'px'

                }
                return null
            },
            isOpened(){

                if(this.defaultOpened && this.parentOpened == undefined){
                    return true
                }

                if(this.parentOpened !== undefined){
                    return this.parentOpened && this.opened
                }

                return this.opened
            },
        },
        methods:{
           
            Toggle(){

                this.$emit('on-opened', this.index)
                this.opened = !this.opened
            }
        },
        mounted() {

            this.$nextTick(() => {

                this.firstHeight = $(`#${this.id}`).height()
                this.opened = this.defaultOpened

            })

        }
    }
</script>
