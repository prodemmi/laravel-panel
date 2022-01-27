<template>
  <div>
    <transition name="fade">
      <Loading
        v-if="$store.getters.getLoading"
        :percent="$store.getters.getLoading"
      />
    </transition>

    <Header />

    <main class="content">
      <keep-alive>
        <transition name="fade">
          <router-view v-if="!$store.getLoading" :key="$route.fullPath" />
        </transition>
      </keep-alive>
    </main>

    <SideBar />
  </div>
</template>

<script>
import Header from "../Header/Header";
import SideBar from "../SideBar/SideBar";
import Loading from "../Loading";

export default {
  components: {
    SideBar,
    Header,
    Loading,
  }
};
</script>
