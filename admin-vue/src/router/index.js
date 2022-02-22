import { createRouter, createWebHistory } from 'vue-router'
import Layout from '@/components/Layout'

// 使用 Glob 动态引入：https://cn.vitejs.dev/guide/features.html#glob-import
const modules = import.meta.glob('/src/views/**/**.vue')
const _import = (file) => modules[`/src/views/${file}.vue`]

// console.debug('modules', modules)

export const noAuthRouters = [
    { path: '/:pathMatch(.*)*', redirect: '/404' },
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
            meta: { permission: ['member:list'] }
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
            meta: { permission: ['role:list'] }
        }]
    }
]

const router = createRouter({
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

// 导航守卫：https://router.vuejs.org/zh/guide/advanced/navigation-guards.html
// 顺序：beforeEach -> beforeResolve -> afterEach
router.beforeEach((to, from, next) => {
    NProgress.start() // 开始Progress
    // 尝试获取cookie中的token
    if (getToken()) {
        // 有token
        if (to.path === '/login') {
            // 但下一跳是登陆页
            // 转到首页
            next({ path: '/' })
        } else {
            // 下一跳不是登陆页
            // VUEX被清除，没有角色名
            if (store.getters.roleName === null) {
                // 重新获取用户信息
                store.dispatch('Detail').then(response => {
                    // 生成路由
                    store.dispatch('GenerateRoutes', response.data).then(() => {
                        router.addRoutes(store.getters.addRouters)
                        next({ ...to })
                    })
                })
            } else {
                next()
            }
        }
    } else {
        // 如果前往的路径是白名单内的,就可以直接前往
        if (whiteList.indexOf(to.path) !== -1) {
            next()
        } else {
            // 如果路径不是白名单内的,而且又没有登录,就转到登录页
            next('/login')
            NProgress.done() // 结束Progress
        }
    }
})

router.beforeResolve(async to => {
    if (to.meta.requiresCamera) {
        try {
            await askForCameraPermission()
        } catch (error) {
            if (error instanceof NotAllowedError) {
                // ... 处理错误，然后取消导航
                return false
            } else {
                // 意料之外的错误，取消导航并把错误传给全局处理器
                throw error
            }
        }
    }
})

router.afterEach((to, from, failure) => {

})


export default router