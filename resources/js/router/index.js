import _ from "lodash"
import Vue from "vue"
import Router from "vue-router"
import routes from "./routes"

Vue.use(Router)

const router = createRouter()

function createRouter() {
    const router = new Router({
        mode: "history",
        base: `/${window.baseUrl}/`,
        routes
    })

    router.beforeEach(beforeEach)
    router.afterEach(afterEach)

    return router
}

async function beforeEach(to, from, next) {

    router.app.$store.commit('setChangingRoute', true)

    if(_.isEmpty(to.matched)){
        next('/')
    }
    
    next()

}

async function afterEach(to, from) {

    setTimeout(() => router.app.$store.commit('setChangingRoute', false), 2000);

}

export default router