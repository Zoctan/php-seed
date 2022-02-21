import { createRouter, createWebHistory } from 'vue-router'

export const noAuthRouters = [
    { path: '/404', component: () => import('@/views/Error/404.vue'), hidden: true },
    { path: '/401', component: () => import('@/views/Error/401.vue'), hidden: true },
    // { path: '/login', component: () => import('@/views/Login.vue'), hidden: true },
    {
        path: '/',
        redirect: '/home'
    },
    {
        path: '/home',
        name: '控制台',
        component: () => import('@/views/Home.vue'),
        meta: {
            title: 'home',
            noCache: true
        }
    }
]

export const authRouters = [
    // {
    //     path: '/account',
    //     component: Layout,
    //     redirect: '/account/list',
    //     icon: 'name',
    //     noDropDown: true,
    //     children: [{
    //         path: 'list',
    //         name: '账户管理',
    //         component: () => import('account/list'),
    //         meta: { permission: ['account:list'] }
    //     }]
    // },
    // {
    //     path: '/account',
    //     component: Layout,
    //     redirect: '/account/detail',
    //     hidden: true,
    //     children: [{
    //         path: 'detail',
    //         name: '账户中心',
    //         component: () => import('account/detail')
    //     }]
    // },
    // {
    //     path: '/role',
    //     component: Layout,
    //     redirect: '/role/list',
    //     icon: 'role',
    //     noDropDown: true,
    //     children: [{
    //         path: 'list',
    //         name: '角色管理',
    //         component: () => import('role/list'),
    //         meta: { permission: ['role:list'] }
    //     }]
    // }
]

export default createRouter({
    /**
     * 历史模式
     * https://router.vuejs.org/zh/guide/essentials/history-mode.html
     * Hash 模式：createWebHashHistory
     * HTML5 模式（推荐）：createWebHistory
     */
    history: createWebHistory(),
    routes: noAuthRouters,
    /**
     * 滚动行为：使用前端路由，当切换到新路由时，想要页面滚到顶部，或者是保持原先的滚动位置，就像重新加载页面那样。
     * https://router.vuejs.org/zh/guide/advanced/scroll-behavior.html
     * 
     * @param {*} to 
     * @param {*} from 
     * @param {*} savedPosition 
     * @returns 
     */
    scrollBehavior(to, from, savedPosition) {
        // 始终滚动到顶部
        return { top: 0 }
    },
})
