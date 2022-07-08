<template>

    <div>

        <div class="flex flex-wrap items-start justify-start w-full">
       
            <component v-for="metric in $store.getters.getConfig.metrics" v-bind="metric.attributes" :is="metric.component" :key="ukey()" :metric="metric"></component>

        </div>

    </div>

</template>

<script>
    export default {}
</script>