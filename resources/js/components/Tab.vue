<template>
    <div class="tab-container">
        <ul class="flex list-none mt-0 p-0 overflow-x-auto">
            <template v-for="tab in tabs">
                <li v-if="tab.show" :key="tab.index">
                    <div style="min-width: 100px"
                        class="transition-all duration-350 box-border text-center py-2 px-4 mb-1 cursor-pointer hover:bg-gray-300"
                        :class="tab.index === activeTab ? 'color-primary' : ''"
                        :style="{boxShadow: tab.index === activeTab ? 'inset 0 -4px 0 -2px var(--primary)' : null }"
                        @click="activeTab = tab.index"
                        v-html="tab.text">
                    </div>
                </li>
            </template>
        </ul>

        <div class="flex flex-col p-2 m-1 h-full">
            <template v-for="(node, index) in tabs">
                <div v-show="node.index === activeTab"
                     v-if="node.show"
                     :key="index">
                    <vnodes v-for="(child, key) in node.children"
                            :key="key"
                            :vnodes="child"/>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
export default {
    props: ['env'],
    data() {
        return {
            activeTab: undefined,
        };
    },
    components: {
        Vnodes: {
            functional: true,
            render    : (h, ctx) => ctx.props.vnodes
        }
    },
    computed  : {
        tabs() {
            return _.map(this.$slots.default, (node) => {
                var index = _.get(node, 'data.attrs.tab', -1)

                var show = !_.flatten(_.map(node.children, 'componentOptions.propsData.fields')).map(field => this.show(field)).every(v => v === false)

                if(show && this.activeTab === undefined){
                    this.activeTab = index
                }

                return {
                    index   : index,
                    text    : index > -1 ? _.get(node, 'data.attrs.tabText') : null,
                    children: node.children,
                    show
                }
            })
        }
    },
    methods: {
        show(field) {

            if ( field.showOnDetail && this.env === 'detail' )
                return true
            else if ( field.showOnIndex && this.env === 'index' )
                return true
            else if ( field.showOnForms && _.includes(['edit', 'create'], this.env) )
                return true
            else
                return false

        },
    },
};
</script>
