<template>

    <div class="flex flex-col">

        <div class="flex items-center flex-wrap">
            
            <template v-for="(url, i) in getValue.slice(0, 6 * page)">

                <div class="relative mx-1" :key="i">
                 
                    <lava-tooltip :text="url">

                        <div v-if="url" @click="copyToClipboard(url)" class="flex items-center justify-center h-24 w-24 text-3xl rounded cursor-pointer border-solid border-2 border-slate-300" v-bind="data.attributes">
                            <img v-if="urlIsImage(url)"
                                v-bind="data.attributes"
                                class="shadow-lg object-cover w-full h-full"
                                v-lazy="url" alt="">
                            <div v-else
                                v-bind="data.attributes">
                                {{getThumbName(url)}}
                            </div>
                        </div>

                    </lava-tooltip>

                    <span v-if="deletable"
                        @click="$emit('on-delete', url)"
                        style="width: 28px;height: 28px"
                        class="ri-close-line absolute z-60 -top-2 ltr:-right-1 rtl:-left-1 cursor-pointer rounded-full bg-primary text-white flex items-center justify-center"></span>

                </div>
            
            </template>

        </div>

        <div v-if="data.multiple && !(getValue.slice(0, 6 * page).length >= getValue.length)" class="flex items-center justify-between cursor-pointer w-full">
            <div class="flex items-center">
                <span class="ltr:mr-1 rtl:ml-1 text-blue-400" @click="showMore">Show more</span>
                <span @click="showAll" class="text-blue-400">Show all</span>
                <lava-spinner v-show="loading" color="primary" class="ltr:ml-4 rtl:mr-4"></lava-spinner>
            </div>
            <div v-if="removable && getValue.length > 0" @click="$emit('remove-all')" class="text-danger">Remove all</div>
        </div>

        <div v-show="data.multiple && getValue && getValue.length">All: {{getValue.length}}</div>

    </div>

</template>

<script>
    export default {
        props: ['data', 'value', 'deletable', 'removable'],
        data(){
            return {
                page: 1,
                loading: false
            }
        },
        computed: {
            getValue(){

                if(this.value === null || this.value === undefined){
                    return []
                }

                if(_.isArray(this.value) && _.isEmpty(this.value)){
                    return []
                }

                if(_.isArray(this.value)){
                    return this.value
                }

                return [this.value]
            }
        },
        methods: {
            showMore(){
                this.loading = true
                setTimeout(() => {
                    this.page++
                    this.loading = false
                }, 400)
            },
            showAll(){
                this.loading = true
                setTimeout(() => {
                    this.page = Number.MAX_SAFE_INTEGER
                    this.loading = false
                }, 400)
            },
            urlIsImage(url) {

                if(!url){
                    return false
                }

                return url.toLowerCase().match(/\.(bmp|svg|jpg|jpeg|png|webp)$/);
            },
            getThumbName(url) {

                if(!url){
                    return null
                }

                return _.upperCase(url.split('.').pop())
            }
        }
    }
</script>