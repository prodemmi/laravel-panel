let routes = []

routes.push({
    path: '/',
    name: 'dashboard',
    component: () => import('../components/Pages/Dashboard')
})

routes.push({
    path: '/tool/:name',
    name: 'tool',
    component: () => import('../components/Pages/Tool')
})

routes.push({
    path: '/:resource',
    name: 'index',
    component: () => import('../components/Pages/Resource')
})

routes.push({
    path: '/:resource/:primaryKey',
    name: 'detail',
    component: () => import('../components/Pages/Detail')
})

routes.push({
    path: '/:resource/:primaryKey/edit',
    name: 'edit',
    component: () => import('../components/Pages/Edit')
})

routes.push({
    path: '/:resource/create',
    name: 'create',
    component: () => import('../components/Pages/Create')
})

routes.push({
    path: '*',
    redirect: 'dashboard'
})

export default routes