<template>

    <transition enter-active-class="animate__animated animate__fadeInLeft animate__faster" leave-active-class="animate__animated animate__fadeOutLeft animate__faster" mode="out-in">

        <div v-if="(isMobile && !$store.getters.getSidebarCollapsed) || !isMobile" 
             v-click-outside="hideSlidbar"
             class="side-bar" :class="isMobile ? 'fixed z-100' : ''">

            <div class="flex flex-col overflow-auto w-full">

                <!--<div v-if="config.logo" class="side-bar__logo"-->
                    <!--:style="{ 'background-image' : 'url(' + config.logo + ')' }"></div>-->

                <div class="flex items-center justify-between w-full">
                    
                    <h4 class="side-bar__text">{{ $store.getters.getConfig.name }}</h4>

                    <lava-button v-if="isMobile" 
                                class="mx-1 text-black"
                                color="white"
                                @click="toggleSidebar" 
                                no-padding>
                        <i class="ri-close-line"></i>
                    </lava-button>

                </div>

                <ListItem />

            </div>

            <Profile />

        </div>

    </transition>

</template>

<script>

    import ListItem from './ListItem'
    import Profile from './Profile'

    export default {
        components : {
            ListItem,
            Profile
        },
        methods: {
            hideSlidbar(){
                
                if(this.isMobile && !this.$store.getters.getSidebarCollapsed){
                    this.$store.commit('toggleSidebar', true)
                }
            }
        }
    }

</script>