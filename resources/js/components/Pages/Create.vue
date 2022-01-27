<template>
    <div class="flex flex-col">
        <i @click="goToBack()" class="ri-arrow-left-line cursor-pointer text-lg w-fit"></i>

        <div class="flex justify-end">
            <lava-button @click="store" :disabled="couldCreate">Create</lava-button>
        </div>

        <div class="p-2 text-lg bg-white shadow rounded-md my-2">
            <fields :data="data" :fields="resource.fields" :errors="errors" env="create" @on-change="changed"/>
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
                canCreated: false,
            };
        },
        computed: {
            couldCreate() {
                if (!this.canCreated) {
                    return true;
                }

                return this.newData.length === 0;
            },
        },
        methods: {
            changed(value, column) {
                this.canCreated = true;
                this.$set(this.newData, column, value);
            },
            store() {
                this.$http
                    .post("/api/store", {
                        resource: this.resource.resource,
                        data: this.newData,
                        primary_key: this.resource.primaryKey,
                        search: decodeURIComponent(this.$route.params.primaryKey),
                    })
                    .then((res) => {
                        if (res) {
                            Lava.toast(res.data.message, "success");
                            this.canCreated = false;
                            this.data = []
                        }
                    })
                    .catch((error) => {
                        this.errors = error.response.data.errors || [];
                        this.canCreated = false;
                    });
            },
        },
    };
</script>
