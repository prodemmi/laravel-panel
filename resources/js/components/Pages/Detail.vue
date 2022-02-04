<template>

    <div class="flex flex-col" v-if="!_.isEmpty(data)">

        <div class="flex items-center justify-between">

            <i @click="goToBack()"
               class="ri-arrow-left-line cursor-pointer text-lg w-fit"></i>

            <lava-button @click="goToRoute('edit', {
                                id: $route.params.id,
                                resource: $route.params.resource})">
                Edit
            </lava-button>

        </div>

        <div class="p-2 text-lg bg-white shadow rounded-md my-2">

            <fields :data="data" :fields="resource.fields" :errors="[]" env="detail"/>

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
                resource: this.activeTool(),
            };
        },
        mounted() {
            this.$http
                .post("/api/detail", {
                    resource: this.resource.resource,
                    search: decodeURIComponent(this.$route.params.primaryKey),
                    primary_key: this.resource.primaryKey,
                })
                .then((res) => {
                    this.data = res.data;
                });
        },
    };
</script>
