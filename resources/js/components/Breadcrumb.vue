<template>
  <ul class="breadcrumb" v-if="crumbs.length">
      <li v-for="(item, i) in crumbs" :key="ukey()">
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
              :key="ukey()"
              v-if="!item.is_root"
              v-text="item.label"
            ></span>
          </transition>
        </a>

        <span v-if="crumbs.length - 1 > i" class="breadcrumb__divider">
          {{ divider }}
        </span>
      </li>
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
      type: [String],
      required: false,
      default: undefined
    },
  },
  computed: {
    crumbs: function () {

      var hasPath = this.path !== undefined

      var path = helper.str_replace_array(hasPath ? this.path || '' : this.$route.matched[0]?.path, ["/detail", "/create", "/edit", "/resources", "/tools"], '')

      const routes = (path && path.length && !_.startsWith(path, '/') ? '/' + path : path)?.split("/");
      let breadcrumbs = [];
      let temp = "";
      for (const route of routes) {
        temp += route + "/";
        const index = routes.indexOf(route)
        const route_object = _.find(this.$router?.options.routes, ({path}) => _.endsWith(path, _.trimEnd(temp, "/") || '/'))
        const label = this.$route?.params[helper.str_replace_array(route, [':', '/'], '')]
        
        breadcrumbs.push({
          label: _.startCase(label || route),
          goTo: () => hasPath ? this.$emit('on-change', route) : this.goToRoute(route_object.name, this.$route?.params),
          name: route_object?.name || route,
          is_root: index === 0,
          icon: index === 0 ? '<i class="ri-home-2-line"></i>' : this.icon(_.find(this.$store.getters.getConfig.resources, { route : label })?.icon)
        })

      }

      return breadcrumbs;
      
    },
  },
};
</script>