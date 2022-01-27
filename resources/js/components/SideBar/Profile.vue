<template>
  <div
    class="side-bar__profile" @click="goToRoute('detail', { primaryKey: resource.primaryKey, resource: resource.resource })">

    <div class="flex items-center">
      <img
      src="https://mdbootstrap.com//img/Photos/Square/1.jpg"
      class="side-bar__profile--avatar"
      alt=""
    />

    <div class="flex flex-col w-full mx-1">
      <div v-if="user.username">
        <span>{{ user.username }}</span>
      </div>
      <div v-else>
        <span>{{ user.first_name }}</span>
        <span>{{ user.last_name }}</span>
      </div>
      <span>{{ user.email }}</span>
    </div>
    </div>

    <a class="text-white no-underline" href="/auth/logout" v-html="icon('logout-box')"></a>
  </div>
</template>

<script>
export default {
  computed: {
    resource() {
      let resource = _.find(this.config.resources, {
        resource: this.config.config.user_resource,
      });

      return {
        primaryKey: this.user[resource.primaryKey],
        resource: resource.route,
      };
    },
  },
};
</script>
