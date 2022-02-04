<template>

    <div class="flex flex-col" v-if="!_.isEmpty(data)">

        <div class="flex items-center justify-between">

            <i @click="goToBack()"
               class="ri-arrow-left-line cursor-pointer text-lg w-fit"></i>

            <div class="flex justify-end">

                <lava-button @click="update" :disabled="couldUpdate">Update</lava-button>

            </div>

        </div>

        <div class="p-2 text-lg bg-white shadow rounded-md my-2">

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
                data: [],
                newData: {},
                resource: this.activeTool(),
                errors: [],
                canUpdate: false,
            };
        },
        computed: {
            couldUpdate() {
                if (!this.canUpdate) {
                    return true;
                }

                return this.newData.length === 0;
            },
        },
        mounted() {
            this.errors = [];
            this.$http
                .post("/api/form", {
                    resource: this.resource.resource,
                    search: decodeURIComponent(this.$route.params.primaryKey),
                    primary_key: this.resource.primaryKey,
                })
                .then((res) => {
                    this.data = res.data;
                });
        },
        methods: {
            changed(value, column) {
                this.canUpdate = true;
                console.log(this.newData);
                this.$set(this.newData, column, value);
            },
            update() {
                this.$http
                    .post("/api/update", {
                        resource: this.resource.resource,
                        data: this.newData,
                        primary_key: this.resource.primaryKey,
                        search: decodeURIComponent(this.$route.params.primaryKey),
                    })
                    .then((res) => {
                        if (res) {
                            Lava.toast(res.data.message, "success");
                            this.canUpdate = false;
                            this.goToBack()
                        }
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors || [];
                        this.canUpdate = false;
                    });
            },
        },
    };
</script>
