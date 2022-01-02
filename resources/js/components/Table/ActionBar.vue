<template>

    <div class="fixed top-8 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded shadow-lg p-2 z-100">

        <div class="py-2 flex items-center justify-between w-full">

            Choose action ({{ selected.length }})

            <i class="ri-close-line cursor-pointer" @click="$emit('on-close')"></i>

        </div>

        <div class="flex items-center">

            <select class="select" style="width: 220px;" v-model="selected_action">

                <option v-for="action in actions"
                        v-if="!action.onlyOnTable"
                        :value="action">{{ action.name }}
                </option>

            </select>

            <lava-button :disabled="!selected_action"
                         @click="$emit('handle-action', selected_action, selected)">Go
            </lava-button>

        </div>

    </div>

</template>

<script>
    export default {
        props: ['actions', 'selected'],
        data() {

            return {
                selected_action: undefined
            }

        }
    };
</script>

