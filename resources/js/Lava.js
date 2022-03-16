import Vue from "vue"
import store from "./store"
import router from "./router"
import VTooltip from "v-tooltip"
import swal from "sweetalert2"
import ClickOutside from 'vue-click-outside'
import VueLazyload from "vue-lazyload"
import {HelperMixin, RouteMixin, ActionMixin} from "./mixins"
import CKEditor from '@ckeditor/ckeditor5-vue2';
import _ from "lodash"

Vue.use(VTooltip)
Vue.use(VueLazyload, {
    loading: 'https://media2.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif?cid=ecf05e47fku4z3nedjhyhyu3d4pl7cijwlsoezdy85s13jeq&rid=giphy.gif&ct=g',
    attempt: 3
  })
Vue.use(CKEditor)

VTooltip.options.defaultHtml = true

Vue.directive('click-outside', ClickOutside)
 
Vue.mixin(HelperMixin)
Vue.mixin(RouteMixin)
Vue.mixin(ActionMixin)

export default class Lava {

    constructor(id = "#app", options = null) {

        this.app = new Vue({
            router,
            store,
            ...options
        }).$mount(id)

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
    confirm(title = "", body = "", danger = false, options = {}) {

        danger = danger || options?.danger || false

        const swalWithBootstrapButtons = swal.mixin({
            customClass: {
                confirmButton: 'button--normal min-w-button py-2 px-4 ' + (danger ? 'bg-danger' : 'bg-primary'),
                cancelButton: 'button--normal bg-primary min-w-button py-2 px-4'
            },
            buttonsStyling: false
        })

        return swalWithBootstrapButtons.fire({
            title: title,
            html: body,
            icon: options?.icon || false,
            showCancelButton: true,
            confirmButtonText: 'Do',
            cancelButtonText: 'Cancel',
            reverseButtons: false,
            allowOutsideClick: false,
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            },
            ...options
        })
    }

    /**
     *
     *
     * @param {boolean} [loading=false]
     * @memberof Lava
     */
    showLoading(loading) {
        if (this.app){

            if(loading == false){
                setTimeout(() => this.app.$store.commit('setLoading', loading), 1000)
                return
            }

            this.app.$store.commit('setLoading', loading)

        }
            
    }

}