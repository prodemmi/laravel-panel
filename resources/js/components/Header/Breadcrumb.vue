<template>
  <ul class="breadcrumb" v-if="crumbs.length">
    <template v-for="(item, i) in crumbs">
      <li>
        <a
          :class="[
            crumbs.length - 1 === i
              ? 'breadcrumb__item--disabled'
              : 'breadcrumb__item',
          ]"
          @click="item.goTo()"
        >
          <span v-if="showIcons || item.is_root" v-html="item.icon"></span>

          <transition name="fade" mode="out-in">
            <span
              :key="item.to"
              v-if="!item.is_root"
              v-text="item.label"
            ></span>
          </transition>
        </a>

        <span v-if="crumbs.length - 1 > i" class="breadcrumb__divider">
          {{ divider }}
        </span>
      </li>
    </template>
  </ul>
</template>

<script>
export default {
  props: {
    divider: {
      type: String,
      required: false,
      default: "/",
    },
    showIcons: {
      type: Boolean,
      required: false,
      default: false,
    },
    path: {
      type: String,
      required: false,
      default: undefined,
    },
  },
  computed: {
    crumbs: function () {

      const routes = (this.$route.matched[0]?.path || "").split("/");
      let breadcrumbs = [];
      let temp = "";
      for (const route of routes) {
        temp += route + "/";
        const index = routes.indexOf(route)
        const route_object = _.find(this.$router.options.routes, { path: _.trimEnd(temp, "/") || '/' })
        const label = this.$route.params[route.replaceAll(':', '').replaceAll('/', '')]
        
        breadcrumbs.push({
          label: _.startCase(label || route),
          goTo: () => this.goToRoute(route_object.name, this.$route.params),
          is_root: index === 0,
          icon: index === 0 ? this.icon('dashboard') : _.find(this.config.resources, { route : label })?.iconTemplate
        })

      }

      return breadcrumbs;
      
    },
  },
};
</script>