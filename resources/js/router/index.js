import Vue from "vue"
import Router from "vue-router"
import routes from "./routes"

Vue.use(Router)

const router = createRouter()

function createRouter() {
    const router = new Router({
        mode: "history",
        base: `/${config.baseUrl}/`,
        routes
    })

    router.beforeEach(beforeEach)
    router.afterEach(afterEach)

    return router
}

let timeout

async function beforeEach(to, from, next) {

    clearTimeout(timeout)
    Lava.showLoading(-1)
    setTimeout(() => {

        next()

    }, 800)

}

async function afterEach(to, from) {

    Lava.showLoading(false)

}

export default router