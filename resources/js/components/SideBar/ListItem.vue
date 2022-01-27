<template>

    <div>

        <div v-if="config.showDashboard"
             class="side-bar__item"
             :class="{ 'side-bar__item--active': activeSidebar === '/' }"
             @click="goToRoute('dashboard')">

            <div class="w-4" v-html="icon('dashboard')"></div>

            <span>
                   Dashboard
            </span>

        </div>

        <template v-for="(resource, group) in config.sidebarItems">

            <div v-if="group" class="side-bar__group">
                {{ group }}
            </div>

            <div v-for="item in resource"
                 :key="item.route"
                 class="side-bar__item"
                 :class="{ 'side-bar__item--active': activeSidebar === item.route }"
                 @click="route(item)">

                <div class="w-4">

                    <div v-if="item.icon" v-html="item.iconTemplate"></div>

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

</template>

<script>

    export default {
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

                this.goToRoute(item.tool ? 'tool' : 'index', {name: item.route, resource: item.route});

            }
        }
    }

</script>