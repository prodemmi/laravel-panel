<template>

    <div>

        <transition name="fade">
            <Loading v-if="$store.getters.getLoading" :percent="$store.getters.getLoading"/>
        </transition>

        <Header/>

        <main class="content">

            <transition name="fade">

                <keep-alive>
                    <router-view v-if="!$store.getters.getChangingRoute" :key="$route.fullPath"/>
                </keep-alive>

            </transition>

        </main>

        <SideBar/>

    </div>

</template>

<script>
    import Header from "../Header/Header"
    import SideBar from "../SideBar/SideBar"
    import Loading from "../Loading"

    export default {
        components: {
            SideBar,
            Header,
            Loading,
        }
    }
</script>
