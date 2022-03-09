<template>

    <div class="relative inset-0" >

        <transition name="fade">
            <div v-if="show" class="fixed inset-0 z-50 bg-gray-600 bg-opacity-50 h-full w-full"></div>
        </transition>

        <transition enter-active-class="animate__animated animate__fadeInDown animate__faster"
                    leave-active-class="animate__animated animate__fadeOutUp animate__faster">

        <div v-if="show"
             class="fixed inset-0 flex items-center z-100 justify-center h-full w-full">

                <div 
                     class="overflow-visible rounded-lg shadow-xl bg-white p-4"
                     style="min-width: 440px;" :style="{ width, height }">

                    <!-- Modal header -->
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-gray-900 m-0">
                            <slot name="header"></slot>
                        </h2>
                        <i class="ri-close-line cursor-pointer" @click="$emit('on-close')"></i>
                    </div>

                    <!-- Modal body -->
                    <div class="w-full" style="z-index: 50000">
                        <slot name="body"></slot>
                    </div>

                    <!-- Modal footer -->
                    <div v-if="showButtons" class="flex items-center justify-end mt-4">
                        <lava-button v-if="confirmLabel" 
                                     @click="$emit('on-continue')"
                                     :disabled="disabled"
                                     :loading="loading"
                                     :color="danger ? 'danger' : 'primary' ">{{ confirmLabel }}</lava-button>
                        <lava-button v-if="cancelLabel" 
                                     @click="$emit('on-cancel')">{{ cancelLabel }}</lava-button>
                    </div>

                </div>

        </div>

        </transition>

    </div>

</template>

<script>
    export default {
        props: {
            width: {
                default: '30%'
            },
            height: {
                default: 'auto'
            },
            show: {
                default: false
            },
            disabled: {
                default: false
            },
            danger: {
                default: false
            },
            showButtons: {
                default: true
            },
            loading: {
                default: false
            },
            confirmLabel: {
                type: String,
                default: 'Do',
            },
            closeOnClickOutside: {
                default: false
            },
            cancelLabel: {
                type: String,
                default: 'Cancel',
            }
        },
        mounted(){

            document.addEventListener('keyup', evt => {
                if (evt.keyCode === 27) {
                    this.$emit('on-close');
                }
            });

        },
        methods: {
            clickOutside(){

                if(this.closeOnClickOutside && this.show){
                    this.$emit('on-close');
                }

            }
        }
    }
</script>