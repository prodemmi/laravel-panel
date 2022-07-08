<template>
    <button
            @click="$emit('click')"
            :disabled="(disabled || loading)"
            :class="[
              `button ${color}`,
              disabled ? 'button--disabled'  : '',
              loading  ? 'button--loading'   : '',
              block    ? 'mx-0 block w-full' : '',
              rounded  ? 'rounded-full'   : '',
              (disabled || loading) ? `pointer-events-none` : null]">
        <lava-spinner v-if="loading"></lava-spinner>
        <slot v-else></slot>
    </button>
</template>

<script>
    export default {
        props: {
            color: {
                type: String,
                required: false,
                default: "primary",
            },
            rounded: {
                type: Boolean,
                required: false,
                default: false,
            },
            block: {
                type: Boolean,
                required: false,
                default: false,
            },
            loading: {
                type: Boolean,
                required: false,
                default: false,
            },
            disabled: {
                type: Boolean,
                required: false,
                default: false,
            }
        }
    };
</script>

