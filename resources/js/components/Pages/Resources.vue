<template>

    <div>

        <div v-for="(resources, group) in $store.getters.getConfig.sidebarItems">
            <span v-if="group && group.length && resources.length">{{group}}: </span>
            <ul class="marker:text-black py-1 my-1">
                <li v-for="resource in _.filter(resources, {tool: false})"
                    :key="resource.class"
                    class="text-blue-800 cursor-pointer"
                    @click="goToRoute('index', {resource: resource.route})">
                    {{resource.pluralLabel}}
                </li>
            </ul>
        </div>

    </div>

</template>
