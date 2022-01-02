import Vue from "vue"
import store from "./store"
import router from "./router"
import VTooltip from "v-tooltip"
import swal from "sweetalert2"
import VDialog from "vuejs-dialog"
import Ripple from "vue-ripple-directive"
import ClickOutside from 'vue-click-outside'
import VueLazyload from "vue-lazyload"
import {RouteMixin} from "./mixins"

Vue.use(VTooltip)
Vue.use(VueLazyload)
VTooltip.options.defaultHtml = true

Vue.use(VDialog, {
    html: true,
    backdropClose: true,
    animation: "bounce",
})

Ripple.color = 'rgba(255, 255, 255, 0.2)'
Vue.directive('ripple', Ripple)
Vue.directive('click-outside', ClickOutside)

Vue.mixin(RouteMixin)

export default class Lava {

    constructor() {
        this.started = false
    }

    start() {
        this.app = new Vue({
            el: "#lava",
            router,
            store,
            mounted() {

                $(document).ready(() => {

                    window.Lava.started = true

                })
            }
        })
    }

    /**
     * Show an message to the user.
     *
     * @param {string} message
     * @param type
     * @param options
     */
    toast(message, type = "info", options) {
        swal.fire({
            toast: true,
            position: "top-end",
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            ...options,
        })
    }

    /**
     *
     *
     * @param {string} [title=""]
     * @param {string} [body=""]
     * @param {string} [promptHelp=""]
     * @param {*} [options={}]
     * @return {*}
     * @memberof Lava
     */
    prompt(title = "", body = "", promptHelp = "", options = {}) {
        return this.app.$dialog.prompt(
            {
                title,
                body,
                okText: "Continue",
                cancelText: "Close",
                ...options,
            },
            {
                promptHelp,
            }
        )
    }

    /**
     *
     *
     * @param {string} [title=""]
     * @param {string} [body=""]
     * @param {string} [promptHelp=""]
     * @param {*} [options={}]
     * @return {*}
     * @memberof Lava
     */
    confirm(title = "", body = "", promptHelp = "", options = {}) {
        return this.app.$dialog.confirm({
            title,
            body,
            backdropClose: false,
            okText: "Continue",
            cancelText: "Close",
            ...options,
        })
    }

    alert(title = "", options = {}) {
        return this.app.$dialog.alert({
            title,
            verification: "sfasf",
            ...options,
        })
    }

    /**
     *
     *
     * @param {boolean} [loading=false]
     * @memberof Lava
     */
    showLoading(loading) {
        if (this.started) {
            this.app.$store.commit('setLoading', loading)
        }
    }

    /**
     *
     *
     * @param {boolean} [loading=false]
     * @memberof Lava
     */
    disableLoading() {
        if (this.started) {
            this.app.$store.commit('setLoading', {})
        }
    }

    goToUrl(url, newTab = false) {

        window.open(url, newTab ? '_blank' : '_self').focus()

    }

    openPopup(url, title, w = 800, h = 600) {
        var left = (screen.width - w) / 2
        var top = (screen.height - h) / 2
        window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left).focus()
    }

    tokenExpired() {
        let options = {
            action: {
                onClick: () => location.reload(),
                text: "Reload",
            },
            duration: null,
        }

        this.alert("Sorry, your session has expired.", "error", options)
    }
}
