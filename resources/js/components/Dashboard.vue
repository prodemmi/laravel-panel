<template>

  <div v-if="checkLicense">Checking license ... <lava-spinner></lava-spinner></div>

  <div v-else-if="noLicense">

    Your License has been expired.
    Try to buy at <a href="https://google.com">here</a>.

  </div>

  <div class="flex overflow-hidden" style="width: 100vw;height: 100vh;" v-else>

    <SideBar v-if="!fullscreen"/>

    <div class="flex flex-col overflow-auto w-full h-full">

      <Header v-if="!fullscreen"/>

      <main class="content w-auto h-full" :class="isTool ? 'overflow-hidden' : 'overflow-y-auto'">
        <transition name="fade" mode="in-out">
          <div class="w-full h-full">
            <lava-breadcrumb v-if="!fullscreen" class="mb-2"/>
            <div class="w-full h-full"><router-view :key="$route.fullPath"></router-view></div>
          </div>
        </transition>
      </main>

    </div>

  </div>

</template>

<script>
import Header  from "./Header/Header";
import SideBar from "./SideBar/SideBar";

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
  computed:{

    isTool(){

      return this.activeTool()?.tool

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

        if(this.windowTitle){
          $(document).prop('title', this.windowTitle);
        }

        this.updateConfig(() => {
          this.checkLicense = false
          this.noLicense = false
        })

    })

  }
};
</script>
