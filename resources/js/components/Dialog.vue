<template>

    <div class="relative inset-0">

        <transition name="fade">
            <div v-if="show" class="fixed inset-0 z-50 bg-gray-600 bg-opacity-50 h-full w-full"></div>
        </transition>

        <transition enter-active-class="animate__animated animate__fadeInDown animate__faster"
                    leave-active-class="animate__animated animate__fadeOutUp animate__faster">

        <div v-if="show" class="fixed inset-0 flex items-center z-100 justify-center h-full w-full">

                <div style="min-width: 440px;"
                     :style="{ width: fullScreen ? '100%' : '440px' }"
                     class="overflow-y-auto overflow-x-hidden rounded-lg shadow-xl bg-white p-4">

                    <!-- Modal header -->
                    <div class="flex justify-between items-start mb-4">
                        <h2 class="text-gray-900 m-0">
                            <slot name="header"></slot>
                        </h2>
                        <span v-html="icon('close')" @click="$emit('on-close')" class="cursor-pointer"></span>
                    </div>

                    <!-- Modal body -->
                    <div class="w-full" style="z-index: 50000">
                        <slot name="body"></slot>
                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center justify-end mt-4">
                        <lava-button @click="$emit('on-continue')"
                                     :disabled="disabled"
                                     :loading="loading"
                                     :color="danger ? 'danger' : 'primary' ">{{ confirmLabel || 'Do' }}</lava-button>
                        <lava-button @click="$emit('on-cancel')">Cancel</lava-button>
                    </div>

                </div>

        </div>

        </transition>

    </div>

</template>

<script>
    export default {
        props: ['show', 'disabled', 'danger', 'confirmLabel', 'loading', 'full-screen'],
        methods: {}
    }
</script>