<template>

    <div class="flex flex-col">

        <div class="flex items-center flex-wrap w-fit">

            <lava-tooltip text="Click to copy url" v-for="(file, i) in value.slice(0, 6 * page)" :key="i" class="relative mx-0.5">

                <div @click="copyToClipboard(file)">
                    <img v-if="isImage(file)"
                         v-bind="data.attributes"
                         class="inline-block h-8 w-8 shadow-lg rounded object-cover cursor-pointer border-solid border-2 border-slate-300"
                         v-lazy="file" alt="">
                    <div v-else
                         v-bind="data.attributes"
                         class="inline-block h-8 w-8 shadow-lg rounded object-cover cursor-pointer border-solid border-2 border-slate-300">
                         {{isImage(file)}}
                    </div>
                </div>

                <span v-if="deletable"
                    @click="$emit('on-delete', i)"
                    style="width: 28px;height: 28px"
                    class="ri-close-line absolute z-60 -top-2 ltr:-right-0.5 rtl:-left-0.5 cursor-pointer rounded-full bg-primary text-white flex w-fit items-center justify-center"></span>

            </lava-tooltip>

        </div>

        <div v-if="!(value.slice(0, 6 * page).length >= value.length)" class="flex items-center justify-between cursor-pointer w-full">
            <div class="flex items-center">
                <span class="ltr:mr-1 rtl:ml-1 text-blue-400" @click="showMore">Show more</span>
                <span @click="showAll" class="text-blue-400">Show all</span>
                <lava-spinner v-show="loading" color="primary" class="ltr:ml-4 rtl:mr-4"></lava-spinner>
            </div>
            <div v-if="removable && value.length > 0" @click="$emit('remove-all')" class="text-danger">Remove all</div>
        </div>

        <div v-show="value && value.length">All: {{value.length}}</div>

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
            async isImage(url) {
                try{

                    var isImage = false
                    var ext = ''
                    let resp= $.ajax(url);
                    await resp;
                    let headers=resp.getAllResponseHeaders().split(/\n/g);
                    for(let i=0;i<=headers.length;i++){
                        let hd=headers[i].split(': ')
                        if (hd[0]=='content-type' && hd[1].indexOf('image')==0){
                            isImage = true;
                        }
                        ext = hd[1];
                    }

                }catch{}

                if(!isImage)
                    return _.capitalize(ext)
                else
                    return true
            }
        }
    }
</script>