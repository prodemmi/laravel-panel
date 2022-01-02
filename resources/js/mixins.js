const RouteMixin = {
    methods: {
        goToRoute(route, params = {}) {

            if (this.getActiveRoute === route) {
                return;
            }

            this.$router.push({name: route, params});

        },
        goToBack() {

            this.$router.back()

        },
        activeTool() {

            let name = this.$route.fullPath.replace('/', '').split('/')[0]

            return _.find(config.resources, {route: name});

        },
        copyToClipboard(text) {

            navigator.clipboard.writeText(text).then(function () {

                Lava.toast("Copying to clipboard was successful!")

            }, function (err) {

            });

        },
        resourceValue(data, column, value = false) {

            if (column.includes('.')) {

                let split = _.split(column, '.')

                split.splice(1, 0, (value ? 'value' : 'display'))

                return _.get(data, split.join('.'))

            }

            return _.get(data, column + (value ? '.value' : '.display'))

        },
        getField(column) {

            return _.find(this.activeTool().fields, {column})

        },
        getComponent(column, prefix) {

            return this.getField(column).component + `-${prefix}`

        }
    }
}

module.exports = {
    RouteMixin
}
