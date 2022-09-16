let routes = []

import Dashboard from '../components/Pages/Dashboard'
import Resource from '../components/Pages/Resource'
import Detail from '../components/Pages/Detail'
import Edit from '../components/Pages/Edit'
import Create from '../components/Pages/Create'

import Tools from '../components/Pages/Tools'

routes.push({
    path: '/',
    name: 'dashboard',
    component: Dashboard
})

routes.push({
    path: '/tools',
    name: 'tools',
    component: Tools
})

// routes.push({
//     path: '/resources',
//     name: 'resources',
//     component: Resources
// })

routes.push({
    path: '/resources/:resource',
    name: 'index',
    component: Resource
})

routes.push({
    path: '/resources/:resource/:primaryKey/detail',
    name: 'detail',
    component: Detail
})

routes.push({
    path: '/resources/:resource/:primaryKey/edit',
    name: 'edit',
    component: Edit
})

routes.push({
    path: '/resources/:resource/create',
    name: 'create',
    component: Create
})

routes.push({
    path: '*',
    redirect: 'dashboard'
})

export default routes