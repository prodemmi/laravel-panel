<template>

    <div class="side-bar">

        <div class="flex flex-col w-full">

            <!--<div v-if="config.logo" class="side-bar__logo"-->
                 <!--:style="{ 'background-image' : 'url(' + config.logo + ')' }"></div>-->
            <h4 class="side-bar__text">{{ config.name }}</h4>

            <ListItem />

        </div>

        <Profile />

    </div>

</template>

<script>

    import ListItem from './ListItem'
    import Profile from './Profile'

    export default {
        components : {
            ListItem,
            Profile
        }
    }

</script>