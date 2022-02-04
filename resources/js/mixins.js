const RouteMixin = {
  methods: {
    goToRoute(name, params = {}) {
      this.$router.push({ name, params }).catch(() => {});
    },
    goToBack() {
      this.$router.back();
    },
    goToUrl(url, newTab = false) {
      window.open(url, newTab ? "_blank" : "_self").focus();
    },
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
    activeTool() {
      let name = this.$route.fullPath.replace("/", "").split("/")[0];

      return _.find(config.resources, { route: name });
    },
    getResource(resource) {

      return _.find(config.resources, { resource });

    },
    activePanel() {

      return window.location.pathname.replace("/", "").split("/")[0];

    },
    copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(
        function () {
          Lava.toast("Copying to clipboard was successful!");
        },
        function (err) {}
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
    handleAction(action, row = null) {

      this.temp_selected = row
      this.selected_action = action

      if(!_.isEmpty(action.fields)){

        this.doAction(action, true);
        return

      }

      this.doAction(action);

    },
    doAction(action, showDialog = false) {

      if(showDialog){
        this.selected_action = action
        return
      }

      if (action.danger) {
        Lava.confirm(action.name, action.help, action.danger).then(res => {
          if(res.isConfirmed)
            this.action()
        });
        return;
      }

      this.action()

    },
    action(){
      this.$http
          .post("/api/action", {
            action: this.selected_action.action,
            values: this.selected_action.values,
            rows: this.temp_selected,
            resource: !!this.relation ? this.relationResource.resource : this.resource.resource
          })
          .then((res) => {

            this.selected = [];
            this.temp_selected = [];
            this.selected_action = undefined;

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
            this.getData();

          });
    },
    icon(icon) {
      return `<i class='ri-${icon}-line'></i>`;
    },
  },
};

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
      
      if(!isEvent){

        val = value

      }

      console.log('val', val)

      this.$emit(
        "on-change",
        val,
        this.data.column
      );
    }, 200),
    setNull() {
      this.model = null;
      this.onChange(this.model);
    },
  },
};

module.exports = {
  RouteMixin,
  FormMixin,
};
