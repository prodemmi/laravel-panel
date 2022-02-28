const HelperMixin = {
  data() {
    return {
      isMobile: window.innerWidth < 740
    }
  },
  methods: {
    openPopup(url, title, w = 800, h = 600) {
      var left = (screen.width - w) / 2;
      var top = (screen.height - h) / 2;
      window
        .open(
          url,
          title,
          "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=" +
          w +
          ", height=" +
          h +
          ", top=" +
          top +
          ", left=" +
          left
        )
        .focus();
    },
    log(data, clear = false, table = false) {

      if (window.debug) {

        if (clear) {
          console.clear()
        }

        if (table)
          console.table(JSON.parse(JSON.stringify(data, true, 2)));
        else
          console.log(data)

      }

    },
    toggleSidebar(){
      this.$store.commit('toggleSidebar', !this.$store.getters.getSidebarCollapsed)
    },
    getLastCounts() {

      return this.$http.post("/api/get-last-counts", { last_count: this.$store.getters.getLastCounts }).then((res) => {

        this.$store.commit('setLastCounts', res.data)

      })

    },
    updateConfig(then = null, optional = false) {

      if (optional) {
        if (typeof then === 'function') {

          then()

        }
        return
      }

      return this.$http.post('api/get-config').then(response => {

        this.$store.commit('setConfig', response.data)
        if (typeof then === 'function') {

          then()

        }

      })

    },
    activeTool() {

      var path = this.$route.fullPath

      if (!path || path.length <= 1) {
        path = window.location.fullPath
      }

      let name = path?.replace("/", "")?.split("/")[path.includes('tool') ? 1 : 0];
      if (name)
        return _.find(this.$store.getters.getConfig.resources, { route: name });

    },
    getResource(resource) {

      return _.find(this.$store.getters.getConfig.resources, { resource });

    },
    activePanel() {

      return window.location.pathname.replace("/", "").split("/")[0];

    },
    copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(
        function () {
          Lava.toast("Copying to clipboard was successful!");
        },
        function (err) { }
      );
    },
    resourceValue(data, field, value = false) {
      value = value ? "value" : "display";

      if (field.column.includes(".")) {

        const split = _.split(field.column, ".").splice(1, 0, value).join(".");

        return _.get(data, split);

      }

      return _.get(data, field.column + "." + value);

    },
    getField(resource, column) {

      return _.find(this.flattenFields(resource.fields), {
        column,
      })

    },
    flattenFields(fields) {
      fields = _.cloneDeep(fields);

      for (let i = 0; i < fields.length; i++) {
        if (fields[i].forDesign) {
          fields[i] = this.flattenFields(fields[i].fields);
        }
      }

      return _.flatten(fields, 1);
    },
    icon(icon, ...options) {

      if (icon)
        return this.$store.getters.getConfig.config.icon_template.replace('$name', icon).replace('$options', options.join(' '));

      return ''

    }
  }
}
const RouteMixin = {
  methods: {
    goToRoute(name, params = {}) {

      this.$router.push({ name, params }).catch(() => { });

    },
    goToBack() {
      this.$router.back();
    },
    goToUrl(url, newTab = false) {
      window.open(url, newTab ? "_blank" : "_self").focus();
    }
  },
};

const ActionMixin = {
  methods: {
    handleAction(action, row = null, goback = false) {

      this.updateConfig(() => {

        this.temp_selected = row
        this.selected_action = action

        if (!_.isEmpty(action.fields)) {

          this.doAction(action, true, goback);
          return

        }

        this.doAction(action, goback);

      })

    },
    doAction(action, showDialog = false, goback) {

      if (this.show_actions !== undefined) {
        this.show_actions = false
      }

      if (showDialog) {
        this.selected_action = action
        return
      }

      if (action.danger) {
        Lava.confirm(action.name, action.help, action.danger).then(res => {
          if (res.isConfirmed)
            this.action(goback)
        });
        return;
      }

      this.action(goback)

    },
    action(goback) {
      Lava.showLoading(-1)
      return this.$http
        .post("/api/action", {
          action: this.selected_action.action,
          values: _.flatten(this.selected_action.values),
          rows: this.temp_selected,
          resource: !!this.relation ? this.relationResource.resource : this.resource.resource
        })
        .then((res) => {

          this.selected = [];
          this.temp_selected = [];
          this.selected_action = undefined;

          Lava.showLoading(false)

          if (goback) {
            this.goToBack()
          }

          if (res.data.type === "newWindow") {
            window.open(res.data.url, res.data.blank ? "_blank" : "_self");
            return;
          }

          if (res.data.type === "dialog") {
            Lava.confirm(res.data.title, res.data.view, false, {
              showCancelButton: false,
              confirmButtonText: 'Ok',
              ...res.data.options
            })
            return;
          }


          if (res.data.type === "route") {
            this.goToRoute(res.data.name, res.data.params);
            return;
          }

          Lava.toast(res.data.message, res.data.type);
          this.updateConfig(typeof this.getData === 'function' ? this.getData() : null)

        });
    }
  }
}

const FormMixin = {
  data() {
    return {
      model: "",
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.model = _.clone(this.value);
    });
  },
  methods: {
    onChange: _.debounce(function (value) {

      let isEvent = value instanceof Event
      let val = this.model

      if (!isEvent) {

        val = value

      }

      var tool = this.activeTool()

      this.$emit(
        "on-change",
        {
          column: this.data.column,
          value: val,
        }
      );

    }, 200),
    setNull() {
      this.model = null;
      this.onChange(this.model);
    },
  },
};

const asHtmlMixin = {
  computed: {
    getValue() {
      return this.data?.asHtml ? { innerHTML: this.value } : { innerText: this.value };
    },
  }
}

module.exports = {
  HelperMixin,
  RouteMixin,
  ActionMixin,
  FormMixin,
  asHtmlMixin
};
