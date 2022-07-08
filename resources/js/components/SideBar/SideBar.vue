<template>

    <transition enter-active-class="animate__animated animate__fadeInLeft animate__faster" leave-active-class="animate__animated animate__fadeOutLeft animate__faster" mode="out-in">

        <div v-if="(isMobile && !$store.getters.getSidebarCollapsed) || !isMobile"
             v-click-outside="hideSlidbar"
             class="side-bar" :class="isMobile ? 'fixed z-100' : ''">

            <div class="flex flex-col overflow-auto w-full">

                <div class="flex justify-between">

                    <img class="side-bar__logo" width="50" src="/lava/lava.png"/>

                    <div class="flex items-center justify-between p-1 w-full">

                        <div>
                            <h2 class="side-bar__lava">Lava</h2>
                            <p class="side-bar__text">{{ $store.getters.getConfig.name }}</p>
                        </div>

                        <lava-button v-if="isMobile"
                                    class="mx-1 text-black"
                                    color="white"
                                    @click="toggleSidebar">

                            <i class="ri-close-line"></i>

                        </lava-button>

                    </div>
                </div>

                <ListItem ref="listItem"
                          :default-item="defaultItem"
                          :show-dashboard="$store.getters.getConfig.showDashboard"
                          :items="items"
                          @on-item-click="route" />

            </div>

            <Profile />

        </div>

    </transition>

</template>

<script>

import ListItem from './ListItem'
import Profile  from './Profile'

export default {
        components : {
            ListItem,
            Profile
        },
        computed: {
            items(){
                return this.$store.getters.getConfig.sidebarItems
            },
            defaultItem() {

                var activeTool = this.activeTool()

                if (activeTool) {

                    return activeTool

                }

                return _.find(this.$store.getters.getConfig.sidebarItems, { route: this.$route.path })

            }
        },
        methods: {
            hideSlidbar(){

                if(this.isMobile && !this.$store.getters.getSidebarCollapsed){
                    this.$store.commit('toggleSidebar', true)
                }
            },
            route(item) {

                if(_.isEmpty(item.group)){

                    this.$refs.listItem.collapseAll()

                }

                if(item.tool){
                    this.goToPath('/tools/' + item.route)
                }else{
                    this.goToRoute('index', {resource: item.route});
                }

                this.toggleSidebar()

            }
        }
    }

</script>
