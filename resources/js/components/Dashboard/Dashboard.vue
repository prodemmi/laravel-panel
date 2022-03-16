<template>

  <div v-if="checkLicense">Checking license ... <lava-spinner></lava-spinner></div>

  <div v-else-if="noLicense">

    Your License has been expired.
    Try to buy at <a href="https://google.com">here</a>.

  </div>

  <div class="flex overflow-hidden" style="width: 100vw;height: 100vh;" v-else>

    <SideBar />

    <div class="flex flex-col overflow-auto w-full h-full">

      <Header />

      <main class="content w-auto h-full" :class="activeTool().tool ? 'overflow-hidden' : 'overflow-y-auto'">
        <transition name="fade" mode="out-in">
          <div v-show="!$store.getters.getChangingRoute"><router-view :key="$route.fullPath"></router-view></div>
        </transition>
      </main>

    </div>
    
  </div>

</template>

<script>
import Header from "../Header/Header";
import SideBar from "../SideBar/SideBar";

export default {
  components: {
    SideBar,
    Header
  },
  data(){
    return {
      checkLicense: true,
      noLicense: false
    }
  },
  mounted(){

    this.$http.post("/api/check-license", {
      key: license.key,
      username: license.username,
      password:license.password
    }).then((res) => {
        
        if(!res.data){

          setTimeout(() => {
            this.checkLicense = false
            this.noLicense = true
          }, 1000)
          return

        }

        this.updateConfig(() => {
          this.checkLicense = false 
          this.noLicense = false
        })

    })

  }
};
</script>
