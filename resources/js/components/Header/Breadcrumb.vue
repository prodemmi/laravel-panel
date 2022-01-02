<template>

    <ul class="breadcrumb" v-if="crumbs.length">

        <template v-for="(item, i) in crumbs">

            <li>

                <a :class="[crumbs.length - 1 === i ? 'breadcrumb__item--disabled' : 'breadcrumb__item']"
                   @click="item.goTo()">

                    <span v-if="showIcons || item.is_root" v-html="item.icon"></span>

                    <transition name="fade" mode="out-in">
                        <span :key="item.to" v-if="!item.is_root" v-text="item.label"></span>
                    </transition>

                </a>

                <span v-if="crumbs.length - 1 > i"
                      class="breadcrumb__divider">
                    {{ divider }}
                </span>

            </li>

        </template>

    </ul>

</template>

<script>
    export default {
        props: {
            divider: {
                type: String,
                required: false,
                default: "/"
            },
            showIcons: {
                type: Boolean,
                required: false,
                default: false
            },
            path: {
                type: String,
                required: false,
                default: undefined
            },
        },
        computed: {
            crumbs: function () {
                // console.clear()

                let current = _.without(_.split(_.first(this.$route.matched).path, '/'), '')

                current.unshift('/')

                let breadcrumbs = _.map(current, (path, index) => {

                    var data = {}

                    var resource = _.find(this.config.resources, {
                        route: path.includes(':') ?
                            this.$route.params.resource : path
                    })

                    data.icon = resource?.iconTemplate

                    data.label = resource?.pluralLabel

                    if (index + 1 === current.length) {

                        data.label = this.$route.params.id
                        data.active = false

                    }

                    data.path = '/' + _.trim((current[index - 1] || '') + '/' + path, '/')

                    data.goTo = () => {

                        this.goToRoute('index', {resource: this.$route.params.resource})

                    }

                    if (index === 0) {

                        data.is_root = true
                        data.goTo = () => {

                            this.goToRoute('/')

                        }

                    }

                    data.active = true
                    return data

                })

                console.log("breadcrumbs ===> ", breadcrumbs);

                return breadcrumbs

            },
        }
    }
</script>
