<template>

    <div>

        <ul class="marker:text-black">
            <li v-for="tool in _.filter($store.getters.getConfig.resources, {tool: true})"
                :key="tool.class"
                class="text-blue-800 cursor-pointer"
                @click="goToPath('/tools/' + tool.route)">
                {{tool.singularLabel}}
            </li>
        </ul>

    </div>

</template>
