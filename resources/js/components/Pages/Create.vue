<template>
    <div class="flex flex-col">
        
        <div class="flex items-center justify-between">

            <i @click="goToBack()" class="cursor-pointer text-lg w-fit" 
               :class="$store.getters.getConfig.rtl ? 'ri-arrow-right-line': 'ri-arrow-left-line'"></i>

            <div class="flex justify-end">
                <lava-button @click="store" :disabled="!couldCreate">Create</lava-button>
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

                if (!this.resource.creatable) {
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
            store() {
                this.$http
                    .post("/api/store", {
                        resource: this.resource.resource,
                        data: this.newData
                    })
                    .then((res) => {
                        if (res) {
                            Lava.toast(res.data.message, "success");
                            // this.canCreate = false;
                            // this.newData = []
                        }
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors || [];
                        // this.canCreate = false;
                    });
            },
        },
    };
</script>
