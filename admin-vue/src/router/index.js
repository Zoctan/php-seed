import { createRouter, createWebHistory } from 'vue-router'
import Layout from 'components/Test'

// 使用 Glob 动态引入：https://cn.vitejs.dev/guide/features.html#glob-import
const modules = import.meta.glob('/src/views/**/**.vue')
const _import = (file) => modules[`/src/views/${file}.vue`]

// console.debug('modules', modules)

export const noAuthRouters = [
    //{ path: '/:pathMatch(.*)*', redirect: '/404' },
    { path: '/404', component: _import('error/404'), meta: { hidden: true } },
    { path: '/401', component: _import('error/401'), meta: { hidden: true } },
    { path: '/login', component: _import('Login'), meta: { hidden: true } },
    { path: '/', redirect: '/home' },
    { path: '/home', name: '控制台', component: _import('Home'), meta: { noCache: true } },
]

export const authRouters = [
    {
        path: '/member',
        component: Layout,
        redirect: '/member/list',
        meta: { icon: 'name', noDropDown: true, },
        children: [{
            path: 'list',
            name: '账户管理',
            component: _import('member/list'),
            meta: { rule: ['member:list'] }
        }]
    },
    {
        path: '/member',
        component: Layout,
        redirect: '/member/detail',
        meta: { icon: 'name', hidden: true, },
        children: [{
            path: 'detail',
            name: '账户中心',
            component: _import('member/detail')
        }]
    },
    {
        path: '/role',
        component: Layout,
        redirect: '/role/list',
        icon: 'role',
        meta: { icon: 'role', noDropDown: true, },
        children: [{
            path: 'list',
            name: '角色管理',
            component: _import('role/list'),
            meta: { rule: ['role:list'] }
        }]
    }
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
        // 始终滚动到顶部
        return { top: 0 }
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

})

router.afterEach((to, from, failure) => {
    NProgress.stop()
})


export default router