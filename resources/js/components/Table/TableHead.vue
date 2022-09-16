<template>
    <tr>

        <slot></slot>

        <template v-for="(header, index) in headers">

            <th :key="index"
                v-if="shows[index].show"
                class="resource-table__th">

                <div class="flex items-center" :class="{'cursor-pointer': header.sortable}" @click="header.sortable ? $emit('on-sort', header.column) : null">

                    <span>{{ header.name }}</span>

                    <span v-if="header.sortable" class="ltr:ml-2 rtl:mr-2">

                            <template v-if="query.sort.column === header.column">

                                <i v-if="query.sort.direction === 'DESC'"
                                class="ri-arrow-up-line">
                                </i>

                                <i v-else-if="query.sort.direction === 'ASC'"
                                class="ri-arrow-down-line">
                                </i>

                            </template>

                            <i v-else class="ri-arrow-drop-down-line"></i>

                        </span>

                </div>

            </th>

        </template>

        <th v-if="resource.actions.length > 0 && showActions" class="resource-table__th" :style="{width: resource.actions.length * 30 + 'px'}">
            Actions
        </th>

    </tr>
</template>

<script>
export default {
    props: ['resource', 'showActions', 'headers', 'shows', 'relation', 'query']
}
</script>

<style>

</style>