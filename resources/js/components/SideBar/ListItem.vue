<template>

    <div>

        <div v-if="showDashboard"
             class="side-bar__item"
             :class="{ 'side-bar__item--active': _.isEmpty(selectedItem) }"
             @click="goToDashboard()">

            <div class="w-6">

                <i class="ri-home-2-line"></i>

            </div>

            <span>
                   Dashboard
            </span>

        </div>

        <div v-for="(subitems, group) in items" :key="group">
            <div v-if="group && subitems.length" class="side-bar-collapse">

                <h4 class="side-bar-collapse--header" @click="toggleGroup(group)">

                    <div class="flex items-center justify-between">

                        <div class="side-bar__group">
                            {{ group }}
                        </div>

                        <i :class="openedGroup === group ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>

                    </div>

                </h4>

                <div ref="container"
                     class="side-bar-collapse--body overflow-hidden transition-all" 
                     :style="{'height': openedGroup === group ? (height * _.filter(subitems, {show: true}).length) + 'px' : '0' }">

                    <template v-for="(item, index) in _.filter(subitems, {show: true})">
                        <div  :key="index"
                         class="side-bar-collapse--item"
                         :class="{ 'side-bar__item--active': _.isMatch(selectedItem, item) }"
                         @click="onItemClick(item)">
                        
                        <div class="w-6">

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

            <template v-else>

                <template v-for="item in subitems">
                    <div v-if="item.show"
                         :key="item.route"
                         class="side-bar__item"
                         :class="{ 'side-bar__item--active': _.isMatch(selectedItem, item) , 'ltr:pl-2 rtl:pr-2' : item.group}"
                         @click="onItemClick(item)">

                        <div class="w-6">

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

            </template>

        </div>

    </div>

</template>

<script>

    export default {
        props: {
            items: [Array, Object],
            showDashboard: {
                default: false
            },
            defaultItem: {
                default: null
            }
        },
        data(){
            return {
                selectedItem: null,
                openedGroup: null,
                height: 32
            }
        },
        mounted() {

            this.$nextTick(() => {

                this.height       = _.first(this.$refs.container)?.scrollHeight / 2 
                this.selectedItem = this.defaultItem
                this.openedGroup  = this.activeGroup
                
            })

        },
        computed: {
            activeGroup(){
                return _.findKey(this.items, subitems => _.some(subitems, this.selectedItem))
            }
        },
        methods: {
            collapseAll(){

                this.openedGroup = null

            },
            toggleGroup(group){

                if(this.openedGroup === group){

                    this.openedGroup = null

                }else{

                    this.openedGroup = group

                }

                this.$emit('on-group-click', group)

            },
            goToDashboard() {

                this.goToRoute('dashboard');
                this.toggleSidebar()
                this.selectedItem = null

            },
            onItemClick(item){

                this.selectedItem = item
                this.$emit('on-item-click', item)

            }
        }
    }

</script>