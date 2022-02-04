<template>

    <div class="flex items-center justify-between w-full">

        <nav>

            <ul class="resource-table__pagination">

                <li>
                    <div
                            class="resource-table__pagination--nav"
                            :class="[ data.current_page > 1 ? 'cursor-pointer' : 'text-gray-400 pointer-events-none' ]"
                            @click="$emit('change-page', data.current_page - 1 )">
                        Previous
                    </div>
                </li>

                <li v-for="link in data.links">
                    <div
                            class="resource-table__pagination--link"
                            :class="[
                        data.current_page === link.link ? 'pointer-events-none text-black bg-gray-300' : '',
                        !link.active ? 'pointer-events-none' : '' ]"
                            @click="$emit('change-page', link.link )"
                            v-html="link.label">
                    </div>
                </li>

                <li>
                    <div
                            class="resource-table__pagination--nav"
                            :class="[ data.current_page < data.total_pages ? 'cursor-pointer' :
                        'text-gray-400 pointer-events-none' ]"
                            @click="$emit('change-page', data.current_page + 1 )">
                        Next
                    </div>
                </li>

            </ul>

        </nav>

        <select class="select"
                v-if="data.all > _.first(activeTool().perPages)"
                style="height: 32px;width: 120px;"
                @click="$emit('change-per-page', $event)">

            <option v-for="per_page in activeTool().perPages"
                    :value="per_page"
                    :selected="per_page == selected">{{ per_page }}
            </option>

        </select>

    </div>

</template>

<script>
    export default {
        props: ['data', 'selected']
    };
</script>

