<template>

    <div class="flex flex-col">

        <div class="flex items-center justify-between">

            <i @click="goToBack()"
               class="cursor-pointer text-lg w-fit"
               :class="$store.getters.getConfig.rtl ? 'ri-arrow-right-line': 'ri-arrow-left-line'"></i>

            <div class="flex justify-end">

                <lava-button v-if="!fullscreen" class="ltr:mr-1 rtl:ml-1 px-4" @click="update(true)" :disabled="couldUpdate">Edit and back</lava-button>
                <lava-button class="ltr:mr-1 rtl:ml-1 px-4" @click="update()" :disabled="couldUpdate">Edit</lava-button>

            </div>

        </div>

        <div v-if="data" class="p-2 text-lg bg-white shadow rounded-md my-2">

            <fields :data="data" :fields="resource.fields" :errors="errors" env="edit" @on-change="changed"/>

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
                data: null,
                newData: [],
                errors: [],
                updating: false,
                resource: this.activeTool(),
                canUpdate: false,
            };
        },
        computed: {
            couldUpdate() {

                if (!this.canUpdate) {
                    return true;
                }

                return this.newData.length === 0;
            }
        },
        mounted() {

            this.$nextTick(() => {

                if (!this.resource.editable) {
                    this.goToBack()
                }
                return

            })
            
            this.errors = [];
            Lava.showLoading(-1)
            this.updateConfig(() => {

                setTimeout(() => this.$http
                    .post("/api/form", {
                        resource: this.resource.resource,
                        search: decodeURIComponent(this.$route.params.primaryKey),
                        primary_key: this.resource.primaryKey,
                    })
                    .then((res) => {
                        this.data = res.data;
                        Lava.showLoading(false)
                }), 500)

            })
                
        },
        methods: {
            changed(value) {
                
                this.canUpdate = true;
                this.newData.push(value)
                this.newData = _.uniqBy(this.newData.reverse(), 'column')

            },
            update(goback = false) {
                this.updating = true
                this.$http
                    .post("/api/update", {
                        resource: this.resource.resource,
                        data: this.newData,
                        primary_key: this.resource.primaryKey,
                        search: decodeURIComponent(this.$route.params.primaryKey),
                    }, {
                        'Content-Type': 'multipart/form-data'
                    })
                    .then((res) => {
                        if (res) {
                            Lava.toast(res.data.message, "success");
                            this.updating = false
                            this.newData = []

                            if(goback){
                                setTimeout(() => this.goToBack(), 400);
                            }

                            // this.canUpdate = false;
                        }
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors || [];
                            this.updating = false
                            // this.canUpdate = false;
                    });
            }
        },
    };
</script>
