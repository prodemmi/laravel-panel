<template>

    <div class="flex flex-col justify-center items-center w-full h-full m-auto">

        <div class="flex flex-col justify-center items-center border-solid border-2 border-black p-4 rounded-md"
             style="min-width: 280px;">
            <i class="ri-emotion-unhappy-line mb-4 text-primary" style="font-size: 4em"></i>
            <b class="mb-4">There is nothing to show.</b>
            <lava-button
                    v-if="resource.creatable"
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