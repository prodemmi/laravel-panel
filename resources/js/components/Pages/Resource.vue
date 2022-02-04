<template>

    <div v-if="activeTool()" class="inline">

        <h2>{{ activeTool().pluralLabel }}</h2>

        <resource-table :resource="activeTool()" :key="$route.fullPath"/>

    </div>

</template>