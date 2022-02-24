import { createRouter, createWebHistory, isNavigationFailure } from 'vue-router'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import Layout from '@/layout/index.vue'

// 使用 Glob 动态引入：https://cn.vitejs.dev/guide/features.html#glob-import
const modules = import.meta.glob('/src/views/**/**.vue')
const _import = (file) => modules[`/src/views/${file}.vue`]
// console.debug('modules', modules)

export const noAuthRouters = [
    { path: '/:allMatch(.*)*', redirect: '/404' },
    { path: '/404', component: _import('error/404'), hidden: true },
    { path: '/401', component: _import('error/401'), hidden: true },
    { path: '/login', name: '登录', component: _import('Login'), hidden: true },
    { path: '/', name: '控制台', redirect: '/dashboard' },
    {
        path: '/dashboard',
        component: Layout,
        name: '控制台',
        icon: 'house',
        children: [{
            path: '',
            component: _import('Dashboard')
        }],
    },
    {
        path: '/test',
        component: Layout,
        name: '测试',
        icon: 'sunny',
        dropDown: true,
        children: [{
            path: 'sub1',
            name: '测试1',
            icon: 'soccer',
            component: _import('TestSub1')
        }, {
            path: 'sub2',
            name: '测试2',
            icon: 'star',
            component: _import('TestSub2')
        }],
    },
]

export const authRouters = [
    // {
    //     path: '/member',
    //     component: Layout,
    //     redirect: '/member/list',
    //     meta: { icon: 'name', dropDown: false, },
    //     children: [{
    //         path: 'list',
    //         name: '账户管理',
    //         component: _import('member/list'),
    //         meta: { rule: ['member:list'] }
    //     }]
    // },
    // {
    //     path: '/member',
    //     component: Layout,
    //     redirect: '/member/detail',
    //     meta: { icon: 'name', hidden: true, },
    //     children: [{
    //         path: 'detail',
    //         name: '账户中心',
    //         component: _import('member/detail')
    //     }]
    // },
    // {
    //     path: '/role',
    //     component: Layout,
    //     redirect: '/role/list',
    //     icon: 'role',
    //     meta: { icon: 'role', noDropDown: true, },
    //     children: [{
    //         path: 'list',
    //         name: '角色管理',
    //         component: _import('role/list'),
    //         meta: { rule: ['role:list'] }
    //     }]
    // }
]

const router = createRouter({
    /**
     * 历史模式：https://router.vuejs.org/zh/guide/essentials/history-mode.html
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
        if (savedPosition) {
            return savedPosition
        } else {
            // 滚动到顶部
            return {
                top: 0,
                behavior: 'smooth',
            }
        }
    },
})

// 导航守卫：https://router.vuejs.org/zh/guide/advanced/navigation-guards.html
// 顺序：beforeEach -> beforeResolve -> afterEach
router.beforeEach((to, from, next) => {
    NProgress.start()
    // 有 token
    if (localStorage.getItem('token')) {
        if (to.path !== '/login') next()
        else next({ path: '/' })
    } else {
        // 如果前往的路径无需认证，直接前往
        if (noAuthRouters.some(item => item.path === to.path)) next()
        else next('/login')
    }
})

router.beforeResolve(async (to, from, next) => {
    next()
})

router.afterEach((to, from, failure) => {
    if (isNavigationFailure(failure)) {
        console.log('failed navigation', failure)
    }
    NProgress.done()
})


export default router