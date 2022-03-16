<template>
    <button
            @click="$emit('click')"
            :disabled="(disabled || loading)"
            :class="[
              `button--normal bg-${color}`,
              disabled ? 'button--disabled' : '',
              loading  ? 'button--loading'  : '',
              block    ? 'button--block'    : '',
              small    ? 'button--small'    : '',
              rounded  ? 'button--rounded'  : '',
              !(disabled || loading) ? `button--hover`    : '',
              noPadding ? 'p-2 w-element h-element': 'min-w-button h-element py-2 px-4'
            ]">
        <lava-spinner v-if="loading" :dot="noPadding"></lava-spinner>
        <slot v-else></slot>
    </button>
</template>

<script>
    export default {
        props: {
            small: {
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
            disabled: {
                type: Boolean,
                required: false,
                default: false,
            },
            noPadding:{
                type: Boolean,
                default: false,
            }
        }
    };
</script>

