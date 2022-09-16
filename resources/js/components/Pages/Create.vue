<template>
    <div class="flex flex-col">

        <i v-show="!fullscreen"
            @click="goToBack()" class="cursor-pointer text-lg w-fit"
            :class="$store.getters.getConfig.rtl ? 'ri-arrow-right-line': 'ri-arrow-left-line'"></i>

        <div class="p-2 text-lg bg-white shadow rounded-md mt-2 mb-4">
            <fields :data="[]" :fields="resource.fields" :errors="errors" env="create" @on-change="changed"/>
        </div>

        <div class="flex justify-end">
            <lava-button :loading="loadingCreateAndBack" v-if="!fullscreen" class="ltr:mr-1 rtl:ml-1 px-4" @click="store(true)" :disabled="!canCreate">Create and back</lava-button>
            <lava-button :loading="loadingCreate" class="px-4" @click="store()" :disabled="!canCreate">Create</lava-button>
        </div>

    </div>
</template>

<script>

    import Fields from './Fields'

    export default {
        components: {
            Fields
        },
        data() {
            return {
                newData: [],
                resource: this.activeTool(),
                errors: [],
                canCreate: false,
                loadingCreate: false,
                loadingCreateAndBack: false,
            };
        },
        mounted() {

            this.$nextTick(() => {

                if (this.resource && !this.resource.creatable) {
                    this.goToBack()
                }

            })

        },
        computed: {
            couldCreate() {

                return !this.errors.length && this.newData.length > 0;

            },
        },
        methods: {
            changed(value) {

                this.canCreate = true;
                this.newData.push(value)
                this.newData = _.uniqBy(this.newData.reverse(), 'column')

            },
            store(back = false) {

                if(back){
                    this.loadingCreateAndBack = true
                }else{
                    this.loadingCreate = true
                }


                this.$http
                    .post("/api/store", {
                        resource: this.resource.resource,
                        data: this.newData
                    })
                    .then((res) => {
                        if (res) {
                            Lava.toast(res.data.message, "success");
                            this.canCreate = false;

                            this.loadingCreateAndBack = false
                            this.loadingCreate = false

                            if(this.fullscreen){

                                setTimeout(() => {

                                    window.opener.$("#send-data-value").remove()

                                    var val = document.createElement('div')
                                    val.innerHTML = JSON.stringify(res.data.data)
                                    val.setAttribute("id", "send-data-value")
                                    window.opener.document.body.appendChild(val);
                                    
                                    window.close()
                                    
                                }, 400);
                                return

                            }

                            if(back){
                                setTimeout(() => this.goToBack(), 400);
                                return
                            }

                            this.newData = []

                        }
                    })
                    .catch((error) => {
                        this.errors = error.response?.data.errors || [];
                        this.canCreate = false;
                        this.loadingCreate = false;
                        this.loadingCreateAndBack = false;
                    });
            },
        },
    };
</script>
