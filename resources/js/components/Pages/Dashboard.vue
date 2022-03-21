<template>

    <div class="flex flex-wrap w-full">

        <component v-for="metric in $store.getters.getConfig.metrics" :is="metric.component" :key="metric.component" :metric="metric"></component>

    </div>

</template>

<script>
    export default {}
</script>