import Vue from 'vue'
import VueRouter from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import appRoutes from './routes.js'
import store from '../store'

Vue.use(VueRouter)

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeView,
        meta: {
            isPublic: true,
        }
    },
    {
        path: '/login',
        name: 'login',
        component: LoginView,
        meta: {
            isPublic: true,
        }
    },
    // application (protected) routes
    ...appRoutes,
    {
        path: '/:catchAll(.*)',
        name: 'notfound',
        // route level code-splitting
        // this generates a separate chunk (about.[hash].js) for this route
        // which is lazy-loaded when the route is visited.
        component: () => import(/* webpackChunkName: "notfound" */ '../views/NotFound.vue'),
        meta: {
            isPublic: true,
        }
    }
]

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes,

})

router.beforeEach((to, from, next) => {
    const isAuthenticated = store.getters['auth/authenticated']
    const isPublicRoute = to.meta.isPublic

    if(isAuthenticated) {
        // ako je korisik prijavljen, a proba poci na rute home ili login
        if (['home','login'].includes(to.name)) {
            next({ name: 'dashboard'})
        } else {
            next()
        }
    } else {
        // ako korisnik nije prijavljen, a proba poci na rutu koja nije javna
        if (! isPublicRoute) {
            next({ name: 'login' })
        } else {
            next()
        }
    }

})

export default router
