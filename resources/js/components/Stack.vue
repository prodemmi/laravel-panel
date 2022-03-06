<template>

    <div :class="isMobile ? 'overflow-auto' : 'overflow-visible'">
        
        <slot v-if="!!$slots['body']" name="body"></slot>
        <slot v-else ></slot>

    </div>

</template>

<script>
    export default {
        props: ['data']
    }
</script>