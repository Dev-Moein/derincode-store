import { createRouter, createWebHistory } from 'vue-router'

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


import AdminDashboard from '../pages/admin/AdminDashboard.vue'
import AdminProjects from '../pages/admin/AdminProjects.vue'
import AdminRequests from '../pages/admin/AdminRequests.vue'
import AdminUsers from '../pages/admin/AdminUsers.vue'
import AdminLayout from '../layouts/admin/AdminLayout.vue'
const routes = [

    {
    path:'/admin',
    component: AdminLayout,

    meta:{
        requiresAuth:true,
        requiresAdmin:true
    },

    children:[

        {
            path:'',
            component:AdminDashboard
        },

        {
            path:'projects',
            component:AdminProjects
        },

        {
            path:'requests',
            component:AdminRequests
        },

        {
            path:'users',
            component:AdminUsers
        },

    ]
},
    {
        path: '/',
        name: 'home',
        component: Home,
    },

    {
        path: '/projects/:slug',
        name: 'project-details',
        component: ProjectDetails,
    },

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
{
    path: '/payment/result',
    name: 'payment-result',
    component: PaymentResult,
},

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

    if (!authStore.initialized) {
        await authStore.fetchUser()
    }
    if(
    to.meta.requiresAdmin &&
    !authStore.isAdmin
){
    return {
        name:'home'
    }
}
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

    if (
        to.name === 'login' &&
        authStore.isAuthenticated
    ) {
        return {
            name: 'profile',
        }
    }
})

export default router
