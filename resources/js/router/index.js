import {createRouter, createWebHistory} from "vue-router";

//user
import homeUserIndex from '../components/user/home/index.vue';


//pages 
// import homepageIndex from '../components/pages/home/index.vue';
//login 
import login from '../components/auth/login.vue';
import register from '../components/auth/register.vue';

import notFound from '../components/notFound.vue';




const routes = [
    // user
    {
        path : '/user/home',
        name : 'userHome',
        component : homeUserIndex,
        meta : {
            requiresAuth : true
        }
    },

    // login 
    {
        path : '/',
        name : 'login',
        component : login,
        meta : {
            requiresAuth : false
        }
    },

    // register 
    {
        path : '/register',
        name : 'register',
        component : register,
        meta : {
            requiresAuth : false
        }
    },
    // notFound 
    {
        path: '/:pathMatch(.*)*',
        name : 'notFound',
        component : notFound,
        meta : {
            requiresAuth : false
        }
    }
]

// const router = createRouter({
//     history : createWebHistory({
//         history : createWebHistory(process.env.BASE_URL)
//     }),
//     routes
// })

const  router = createRouter({
    history : createWebHistory(),
    routes
})
router.beforeEach((to, from) => {
    if(to.meta.requiresAuth && !localStorage.getItem('token')){
        return { name : 'login' }
    }
    if(to.meta.requiresAuth == false && localStorage.getItem('token')){
        return { name : 'userHome'}
    }
})
export default router;