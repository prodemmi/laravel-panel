<template>

    <div class="progress-back">
        <div :class="[ percent === -1 ? 'indeterminate' : 'determinate' ]"
             :style="{ width: percent === -1 ? null :`${percent}%` }">
        </div>
    </div>


</template>

<script>

    export default {
        props: ['percent']
    }

</script>
