<template>

    <div class="header">

        <transition name="fade">
            <lava-loading class="absolute left-0 top-0 right-0 h-loading" v-show="$store.getters.getLoading" :percent="$store.getters.getLoading"/>
        </transition>

        <div class="flex items-center justify-between w-full h-full px-2">

            <div class="flex items-center w-full">

                <lava-button v-if="isMobile" 
                        class="mx-1"
                        @click="toggleSidebar">
                    <i class="ri-menu-line"></i>
                </lava-button>

                <lava-search ref="search" :initable="false" @on-change="onGlobalSearchChange" style="width: 240px" :multiple="false" placeholder="Global search" :disabled="false" uri="api/global-search"></lava-search>

            </div>

        </div>

    </div>

</template>

<script>
export default {
    methods: {
        onGlobalSearchChange(option){

            this.goToRoute(option.name, option.params)
            this.$refs.search.clear()

        }
    }
}
</script>