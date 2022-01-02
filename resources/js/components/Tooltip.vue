<template>
    <div v-tooltip.`${position}`="text">
        <slot></slot>
    </div>
</template>
<script>
    export default {
        props:{
            position: {
                default: 'auto'
            },
            text:{
                default: ''
            }
        }
    }
</script>