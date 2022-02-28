<template>

    <div>

        <div v-if="$store.getters.getConfig.showDashboard"
             class="side-bar__item"
             :class="{ 'side-bar__item--active': activeSidebar === '/' }"
             @click="goToRoute('dashboard')">

            <i class="ri-home-2-line w-4"></i>

            <span>
                   Dashboard
            </span>

        </div>

        <div v-for="(resource, group) in $store.getters.getConfig.sidebarItems" :key="group">

            <side-bar-collapse v-if="group"
                               :defaultOpened="defaultOpened(resource)"
                               :index="group"
                               :parentOpened="openedIndex !== undefined ? openedIndex == group : undefined"
                               @on-opened="index => openedIndex = index">

                <template v-slot:header>

                    <div class="side-bar__group">
                        {{ group }}
                    </div>
            
                </template>

                <template v-slot:body>

                    <div v-for="item in resource"
                        :key="item.route"
                        class="side-bar__item pl-4"
                        :class="{ 'side-bar__item--active': activeSidebar === item.route }"
                        @click="route(item)">

                        <div class="w-4">

                            <div v-if="item.icon" v-html="icon(item.icon)"></div>

                        </div>

                        <div class="flex items-center justify-between w-full">

                            <span v-if="item.tool">
                            {{ item.singularLabel }}
                            </span>

                            <span v-else>
                                {{ item.pluralLabel }}
                            </span>

                            <template name="fade">

                                <span v-if="getLastCounts(item.resource) > 0"
                                      class="flex items-center justify-center bg-danger p-0.5 w-3 h-3 shadow rounded-full">
                                    {{ getLastCounts(item.resource) }}
                                </span>

                            </template>

                        </div>

                    </div>
            
                </template>

            </side-bar-collapse>

            <template v-else>

                <div v-for="item in resource"
                    :key="item.route"
                    class="side-bar__item pl-2"
                    :class="{ 'side-bar__item--active': activeSidebar === item.route }"
                    @click="route(item)">

                    <div class="w-4">

                        <div v-if="item.icon" v-html="icon(item.icon)"></div>

                    </div>

                    <span v-if="item.tool">
                        {{ item.singularLabel }}
                    </span>

                    <span v-else>
                        {{ item.pluralLabel }}
                    </span>

                </div>

            </template>

        </div>

    </div>

</template>

<script>

    import SideBarCollapse from './SideBarCollapse'

    export default {
        components: {
            SideBarCollapse
        },
        data(){
            return {
                openedIndex: undefined
            }
        },
        computed: {
            activeSidebar() {

                if (this.activeTool()) {


                    return this.activeTool().route

                }

                return this.$route.path

            }
        },
        methods: {
            route(item) {

                if(_.isEmpty(item.group)){
                    this.openedIndex = null
                }

                this.goToRoute(item.tool ? 'tool' : 'index', {name: item.route, resource: item.route});

            },
            defaultOpened(resources){

                return !!_.find(resources, {route: this.activeSidebar})

            },
            getLastCounts(resource){
                
                var t = _.find(this.$store.getters.getLastCounts, { resource })

                if(t){

                    if(t?.new_count > 0){
                        return t.new_count
                    }

                    return 0
                
                }

                return 0

            }
        }
    }

</script>