import {
    createRouter,
    createWebHistory,
} from 'vue-router'

import { useAuthStore } from '../stores/auth'

import Home from '../pages/Home.vue'
import Login from '../pages/Login.vue'
import Profile from '../pages/Profile.vue'
import ProjectDetails from '../pages/ProjectDetails.vue'
import ForgotPassword from '../pages/ForgotPassword.vue'
import VerifyOtp from '../pages/VerifyOtp.vue'
import ResetPassword from '../pages/ResetPassword.vue'
import Register from '../pages/Register.vue'
import PaymentResult from '../pages/PaymentResult.vue'

import AdminLayout from '../layouts/admin/AdminLayout.vue'
import AdminDashboard from '../pages/admin/AdminDashboard.vue'
import AdminProjects from '../pages/admin/AdminProjects.vue'
import AdminRequests from '../pages/admin/AdminRequests.vue'
import AdminUsers from '../pages/admin/AdminUsers.vue'
import RequestProject from '../pages/RequestProject.vue'
import AdminUserDetails from '../pages/admin/AdminUserDetails.vue'


const routes = [

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    {
        path: '/admin',

        component: AdminLayout,

        meta: {
            requiresAuth: true,
            requiresAdmin: true,
        },

        children: [

            {
                path: '',
                name: 'admin-dashboard',
                component: AdminDashboard,
            },

            {
                path: 'projects',
                name: 'admin-projects',
                component: AdminProjects,
            },

            {
                path: 'requests',
                name: 'admin-requests',
                component: AdminRequests,
            },

            {
                path: 'users',
                name: 'admin-users',
                component: AdminUsers,
            },
       {
    path:'users/:id',
    name:'admin-user-details',
    component:AdminUserDetails
}

        ],
    },


    /*
    |--------------------------------------------------------------------------
    | Public
    |--------------------------------------------------------------------------
    */

    {
        path: '/',
        name: 'home',
        component: Home,
    },
    {
    path:'/projects',
    name:'projects',
    component:()=>import('../pages/Projects.vue')
},
    {
        path: '/projects/:slug',
        name: 'project-details',
        component: ProjectDetails,
    },


    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    {
        path: '/login',
        name: 'login',
        component: Login,
    },

    {
        path: '/register',
        name: 'register',
        component: Register,
    },

    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPassword,
    },

    {
        path: '/verify-otp',
        name: 'verify-otp',
        component: VerifyOtp,
    },

    {
        path: '/reset-password',
        name: 'reset-password',
        component: ResetPassword,
    },


    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    */

    {
        path: '/payment/result',
        name: 'payment-result',
        component: PaymentResult,
    },

        {
 path:'/request-project',
 name:'request-project',
 component:RequestProject,
 meta:{
    requiresAuth:true
 }
},
    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    {
        path: '/profile',
        name: 'profile',
        component: Profile,

        meta: {
            requiresAuth: true,
        },
    },

]


const router = createRouter({

    history: createWebHistory(),

    routes,

    scrollBehavior(to) {

        if (to.hash) {

            return {
                el: to.hash,
                behavior: 'smooth',
            }

        }

        return {
            top: 0,
        }

    },

})


router.beforeEach(async (to) => {

    const authStore = useAuthStore()


    /*
    |--------------------------------------------------------------------------
    | Initialize authentication
    |--------------------------------------------------------------------------
    */

    if (!authStore.initialized) {

        await authStore.fetchUser()

    }


    /*
    |--------------------------------------------------------------------------
    | Authentication Guard
    |--------------------------------------------------------------------------
    */

    if (
        to.meta.requiresAuth &&
        !authStore.isAuthenticated
    ) {

        return {
            name: 'login',

            query: {
                redirect: to.fullPath,
            },
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Admin Guard
    |--------------------------------------------------------------------------
    */

    if (
        to.meta.requiresAdmin &&
        !authStore.isAdmin
    ) {

        return {
            name: 'home',
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Prevent authenticated user from visiting login
    |--------------------------------------------------------------------------
    */

    if (
        to.name === 'login' &&
        authStore.isAuthenticated
    ) {

        if (authStore.isAdmin) {

            return {
                name: 'admin-dashboard',
            }

        }

        return {
            name: 'profile',
        }

    }

})


export default router
