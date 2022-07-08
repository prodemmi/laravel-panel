<template>
    <div class="flex flex-col">

        <div class="flex items-center justify-between">

            <i v-if="!fullscreen" 
               @click="goToBack()" class="cursor-pointer text-lg w-fit"
               :class="$store.getters.getConfig.rtl ? 'ri-arrow-right-line': 'ri-arrow-left-line'"></i>

            <div v-if="fullscreen"></div>

            <div class="flex justify-end">
                <lava-button v-if="!fullscreen" class="ltr:mr-1 rtl:ml-1 px-4" @click="store(true)" :disabled="!couldCreate">Create and back</lava-button>
                <lava-button class="px-4" @click="store()" :disabled="!couldCreate">Create</lava-button>
            </div>

        </div>

        <div class="p-2 text-lg bg-white shadow rounded-md my-2">
            <fields :data="[]" :fields="resource.fields" :errors="errors" env="create" @on-change="changed"/>
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

                return this.newData.length > 0;

            },
        },
        methods: {
            changed(value) {

                this.canUpdate = true;
                this.newData.push(value)
                this.newData = _.uniqBy(this.newData.reverse(), 'column')

            },
            store(back = false) {
                this.$http
                    .post("/api/store", {
                        resource: this.resource.resource,
                        data: this.newData
                    })
                    .then((res) => {
                        if (res) {
                            Lava.toast(res.data.message, "success");
                            this.canCreate = false;
                            this.newData = []

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
                            }

                        }
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors || [];
                        this.canCreate = false;
                    });
            },
        },
    };
</script>
