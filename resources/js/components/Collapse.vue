<template>

  <div class="card">

    <div class="flex-center px-1">

      <div class="card__header w-full cursor-pointer" @click="opened = !opened">

        <div class="flex justify-between ">

            <slot name="header"></slot>

            <i :class="opened ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>

        </div>

      </div>

    </div>

    <div class="card__body" v-show="opened">

      <slot name="body"></slot>

      <div class="card__footer" v-if="!!$slots.footer">
        
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
