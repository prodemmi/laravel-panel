import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        loading: false,
        config: [],
        sidebar_collapsed: true,
        changing_route: false
    },
    getters: {
        getLoading: state => {
            return state.loading
        },
        getConfig: state => {
            return state.config
        },
        getSidebarCollapsed: state => {
            return state.sidebar_collapsed
        },
        getChangingRoute: state => {
            return state.changing_route
        }
    },
    mutations: {
        setLoading(state, loading) {

            state.loading = loading
            
        },
        setConfig(state, config) {
            state.config = config
        },
        toggleSidebar(state, toggle) {

            state.sidebar_collapsed = toggle

        },
        setChangingRoute(state, changing) {

            state.changing_route = changing

        }
    }
})