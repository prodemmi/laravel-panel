<template>

    <div class="flex justify-between w-full" :style="{ 'flex-direction': data.direction }">

        <slot v-if="$slots['body']" name="body"></slot>
        <slot v-else ></slot>

    </div>

</template>

<script>
    export default {
        name: 'lava-stack',
        props: ['data']
    }
</script>
