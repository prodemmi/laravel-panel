<template>
    <div class="spinner">
        <div class="bounce1" :class="[`bg-${color}`]"></div>
        <div v-if="!dot" class="bounce2" :class="[`bg-${color}`]"></div>
        <div v-if="!dot" :class="[`bg-${color}`]"></div>
    </div>
    
</template>

<script>

    export default {
        props: {
            color: {
                default: 'white'
            },
            dot:{
                default: false
            }
        }
    }

</script>
