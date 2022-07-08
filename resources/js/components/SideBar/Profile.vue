<template>
  <div
    class="side-bar__profile" @click="goToRoute('detail', { primaryKey: resource.primaryKey, resource: resource.resource })">

    <div class="flex items-center">
      <img
      src="https://mdbootstrap.com//img/Photos/Square/1.jpg"
      class="side-bar__profile--avatar"
      alt=""
    />

    <div class="flex flex-col w-full mx-1 truncate">
      <div v-if="resource.primaryKey">
          <span>{{ resource.primaryKey }}</span>
        </div>
        <div v-else>
          <span>{{ user.first_name }}</span>
          <span>{{ user.last_name }}</span>
        </div>
        <span class="text-sm">{{ user.email }}</span>
      </div>
    </div>

    <a class="text-white no-underline" href="/auth/logout">
      <i class="ri-logout-box-line"></i>
    </a>
  </div>
</template>

<script>
export default {
  computed: {
    resource() {
      let resource = _.find(this.$store.getters.getConfig.resources, {
        resource: this.$store.getters.getConfig.config.user_resource
      });

      return {
        primaryKey: this.user[resource.primaryKey],
        resource: resource.route,
      };
    },
  },
};
</script>
