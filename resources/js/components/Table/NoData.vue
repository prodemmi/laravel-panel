<template>

    <div class="flex flex-col justify-center items-center w-full h-full" style="min-width: 200px;">

        <div class="flex flex-col justify-center items-center border-solid border-2 border-black p-4 rounded-md">
            <i class="ri-emotion-unhappy-line mb-4 text-4xl text-primary"></i>
            <lava-button @click="goToRoute('create', { resource: resource.route })">
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