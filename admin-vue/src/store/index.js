import { Store } from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import state from '@/state'
import getters from '@/getters'
import actions from '@/actions'
import mutations from '@/mutations'
import app from '@/modules/app'
import member from '@/modules/member'
import router from '@/modules/router'

export default new Store({
    // 持久化插件：https://github.com/robinvdvleuten/vuex-persistedstate/tree/3.x.x
    plugins: [createPersistedState({
        // 默认存储到 LocalStorage
        storage: window.localStorage
    })],
    state: state,
    getters: getters,
    mutations: mutations,
    actions: actions,
    modules: {
        app,
        member,
        router
    },
})
