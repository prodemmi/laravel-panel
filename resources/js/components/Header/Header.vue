<template>

    <div class="header">

        <transition name="fade">
            <Loading v-show="$store.getters.getLoading"
                     :percent="$store.getters.getLoading"/>
        </transition>

        <div class="flex items-center">

            <lava-button v-if="isMobile" 
                         class="mx-1"
                         @click="toggleSidebar" 
                         no-padding>
                <i class="ri-menu-line"></i>
            </lava-button>

            <Breadcrumb/>

        </div>

    </div>

</template>

<script>

    import Breadcrumb from './Breadcrumb'
    import Loading from "../Loading";

    export default {
        components:{
            Breadcrumb,
            Loading
        }
    }

</script>