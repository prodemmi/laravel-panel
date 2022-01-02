import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        loading: false
    },
    getters: {
        getLoading: state => {
            return state.loading
        }
    },
    mutations: {
        setLoading(state, loading) {
            state.loading = loading
        }
    }
})