import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        loading: false,
        config: [],
        sidebar_collapsed: true,
        last_counts: []
    },
    getters: {
        getLoading: state => {
            return state.loading
        },
        getConfig: state => {
            return state.config
        },
        getLastCounts: state => {
            return state.last_counts
        },
        getLastCounts: state => {
            return state.last_counts
        },
        getSidebarCollapsed: state => {
            return state.sidebar_collapsed
        }
    },
    mutations: {
        setLoading(state, loading) {

            state.loading = loading
            
        },
        setConfig(state, config) {
            state.config = config
        },
        addLastCounts(state, data) {

            var newData = JSON.parse(localStorage.getItem('last_counts')) || []

            newData.push(data)
            newData = _.uniqBy(newData.reverse(), 'resource')
            
            localStorage.setItem('last_counts', JSON.stringify(newData))
            state.last_counts = newData
        },
        setLastCounts(state, data) {

            localStorage.setItem('last_counts', JSON.stringify(data))
            state.last_counts = data

        },
        toggleSidebar(state, toggle) {

            state.sidebar_collapsed = toggle

        }
    }
})