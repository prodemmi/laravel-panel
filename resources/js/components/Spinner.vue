<template>
    <div class="spinner w-fit">
        <div class="bounce1" :class="[`bg-${color}`]"></div>
        <div class="bounce2" :class="[`bg-${color}`]"></div>
        <div :class="[`bg-${color}`]"></div>
    </div>
    
</template>

<script>

    export default {
        props: {
            color: {
                default: 'white'
            }
        }
    }

</script>
