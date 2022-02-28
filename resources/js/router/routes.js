let routes = []

import Dashboard from '../components/Pages/Dashboard'
import Tool from '../components/Pages/Tool'
import Resource from '../components/Pages/Resource'
import Detail from '../components/Pages/Detail'
import Edit from '../components/Pages/Edit'
import Create from '../components/Pages/Create'

routes.push({
    path: '/',
    name: 'dashboard',
    component: Dashboard
})

routes.push({
    path: '/tool/:name',
    name: 'tool',
    component: Tool
})

routes.push({
    path: '/:resource',
    name: 'index',
    component: Resource
})

routes.push({
    path: '/:resource/:primaryKey',
    name: 'detail',
    component: Detail
})

routes.push({
    path: '/:resource/:primaryKey/edit',
    name: 'edit',
    component: Edit
})

routes.push({
    path: '/:resource/create',
    name: 'create',
    component: Create
})

routes.push({
    path: '*',
    redirect: 'dashboard'
})

export default routes