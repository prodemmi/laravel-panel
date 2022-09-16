const _ = require("lodash");
const HelperMixin = {
    data() {
        return {
            isMobile: window.innerWidth < 740,
            fullscreen: JSON.parse(this.$route.query?.fullscreen || 'false'),
            windowTitle: this.$route.query?.title,
        }
    },
    methods: {
        ukey() {

            var idstr = String.fromCharCode(Math.floor((
                Math.random() * 25
            ) + 65));

            do {

                var ascicode = Math.floor((
                    Math.random() * 42
                ) + 48);
                if (ascicode < 58 || ascicode > 64) {

                    idstr += String.fromCharCode(ascicode);
                }
            } while (idstr.length < 32);

            return (
                idstr
            );

        },
        getThemeColor(variable) {

            rgbString = window.getComputedStyle(document.documentElement).getPropertyValue(variable)

            var arr = [];

            rgbString.replace(/[\d+\.]+/g, function (v) {
                arr.push(parseFloat(v));
            });

            return "#" + arr.map(this.toHex).join("")

        },
        toHex(int) {
            var hex = int.toString(16);
            return hex.length == 1 ? "0" + hex : hex;
        },
        uplaodFile(files, data = {}) {

            const formData = new FormData();
            _.each(files, (file, i) => formData.append(`file-${i}`, file))
            _.each(data, (d, i) => formData.append(i, d))

            return this.$http.post('api/media/upload', formData, {
                onUploadProgress: progressEvent => Lava.showLoading(progressEvent.loaded),
                'Content-Type': 'multipart/form-data'
            })

        },
        openPopup(url, title, w = null, h = null) {

            if (w === null) {
                w = screen.width - (screen.width / 4)
            }

            if (h === null) {
                h = screen.width - (screen.width / 1.8)
            }

            var left = (
                screen.width - w
            ) / 2;
            var top = (
                screen.height - h
            ) / 2;

            return window
                .open(url, title, "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=" + w + ", height=" + h + ", top=" + top + ", left=" + left)
        },
        log(data, clear = false, table = false) {

            if (window.debug) {

                if (clear) {
                    console.clear()
                }

                if (table) console.table(JSON.parse(JSON.stringify(data, true, 2))); else console.log(data)

            }

        },
        dd(data) {

            Lava.confirm('Dump', _.isObject(data) ? JSON.stringify(data, 5, 10) : data, false, {
                showCancelButton: false,
                confirmButtonText: 'Ok',
                cancelButtonText: null,
                allowOutsideClick: true,
            })

        },
        toggleSidebar() {
            this.$store.commit('toggleSidebar', !this.$store.getters.getSidebarCollapsed)
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

            let name = path?.replace("/", "")?.split("/")[1];
            if (name) return _.find(this.$store.getters.getConfig.resources, { route: name });

        },
        getResource(resource) {

            return _.find(this.$store.getters.getConfig.resources, { resource });

        },
        activePanel() {

            return window.location.pathname.replace("/", "").split("/")[0];

        },
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function () {
                Lava.toast("Copying to clipboard was successful!");
            }, function (err) {
            });
        },
        previewImage(url) {

            var body = `Url: <a href='${url}'>${url}</a>`

            Lava.confirm('', body, false, {
                imageUrl: url,
                imageHeight: '60vh',
                showCancelButton: false,
                showConfirmButton: true,
                confirmButtonText: 'Copy Link',
                allowOutsideClick: true,
            }).then((res) => {

                if (res.isConfirmed) {
                    this.copyToClipboard(url)
                }

            })

        },
        humanReadableSize(bytes) {
            let size = parseInt(data)
            for (let unit of ['B', 'KB', 'MB', 'GB', 'TB']) {
                if (size < 1024)
                    return `${size.toFixed(2)} ${unit}`
                size /= 1024.0
            }
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

            if (icon) return this.$store.getters.getConfig.config.icon_template.replace('$name', icon).replace('$options', options.join(' '));

            return ''

        }
    }
}
const RouteMixin = {
    methods: {
        goToRoute(name, params = {}) {

            this.$router.push({
                name,
                params
            }).catch(() => {
            });

        },
        goToPath(path) {

            this.$router.push({ path }).catch(() => {
            });

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
        // handleAction(action, rows = null, goback = false) {

        //     if ( !_.isEmpty(action.fields) ) {

        //         this.selected_action_dialog = action
        //         return

        //     }

        //     if ( action.danger ) {
        //         Lava.confirm(action.name, action.help, action.danger).then(res => {
        //             if ( res.isConfirmed ) this.doAction(action, rows)
        //         });
        //         return;
        //     }

        //     this.doAction(action, rows)

        // },
        // doAction(action = null, rows = null, goback = false) {

        //     Lava.showLoading(-1)

        //     return this.$http
        //         .post("/api/action", {
        //             action: action,
        //             values: _.flatten(action.values),
        //             rows  : rows
        //         })
        //         .then((res) => {

        //             Lava.showLoading(false)

        //             if ( this.goback ) {
        //                 this.goToBack()
        //                 return
        //             }

        //             if ( res.data.type === "newWindow" ) {
        //                 window.open(res.data.url, res.data.blank ? "_blank" : "_self");
        //                 return;
        //             }

        //             if ( res.data.type === "dialog" ) {
        //                 Lava.confirm(res.data.title, res.data.view, false, {
        //                     showCancelButton : false,
        //                     confirmButtonText: 'Ok', ...res.data.options
        //                 })
        //                 return;
        //             }


        //             if ( res.data.type === "route" ) {
        //                 this.goToRoute(res.data.name, res.data.params);
        //                 return;
        //             }

        //             Lava.toast(res.data.message, res.data.type)
        //             this.updateConfig(typeof this.getData === 'function' ? this.getData() : null)

        //         });
        // }
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
            console.log({
                column: this.data?.column,
                value: val
            })

            this.$emit("on-change", {
                column: this.data?.column,
                value: val
            });

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