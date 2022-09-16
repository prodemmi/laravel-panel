<template>

    <div class="flex flex-col justify-center items-center w-full m-auto" style="height: 70vh">

        <div class="flex flex-col justify-center items-center border-solid border-2 border-black p-8 rounded-md"
             style="min-width: 280px;">
            <i class="ri-emotion-unhappy-line mb-4 text-primary text-6xl"></i>
            <b >There is nothing to show.</b>
            <lava-button
                    v-if="resource.creatable"
                    class="mt-8 px-4"
                    @click="goToRoute('create', { resource: resource.route })">
                Create {{ resource.singularLabel }}
            </lava-button>
        </div>

    </div>

</template>

<script>
    export default {
        props: ['resource']
    }
</script>
