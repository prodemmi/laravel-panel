import Vue from "vue"
import store from "./store"
import router from "./router"
import VTooltip from "v-tooltip"
import swal from "sweetalert2"
import ClickOutside from 'vue-click-outside'
import VueLazyload from "vue-lazyload"
import {HelperMixin, RouteMixin} from "./mixins"
import CKEditor from '@ckeditor/ckeditor5-vue2';
import _ from "lodash"

Vue.use(VTooltip)
Vue.use(VueLazyload, {
    loading: '/lava/loading.webp',
    attempt: 1
  })
Vue.use(CKEditor)

VTooltip.options.defaultHtml = true

Vue.directive('click-outside', ClickOutside)

Vue.mixin(HelperMixin)
Vue.mixin(RouteMixin)

export default class Lava {

    constructor(){
        this.app = null
        this.tools = []
        this.components = []
    }

    createApp(id) {

        // _.forEach(this.tools, tool => router.addRoute(tool) )
        // _.forEach(this.components, ({name, component}) => Vue.component(name, component) )
        router.addRoutes(this.tools)
        this.app = new Vue({
            router,
            store
        }).$mount(id)

        return this

    }

    addComponent(name, component){
        
        this.components.push({
            name,
            component
        })

        return this

    }

    addTool(route){
        
        this.tools.push(route)

        return this
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
                confirmButton: 'button min-w-button py-2 px-4 mx-1 ' + (danger ? 'danger' : 'primary'),
                cancelButton: 'button primary min-w-button py-2 px-4 mx-1'
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
            allowOutsideClick: true,
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

        if(loading == false){
            setTimeout(() => this.app.$store.commit('setLoading', loading), 1000)
            return
        }

        this.app.$store.commit('setLoading', loading)

    }

}
