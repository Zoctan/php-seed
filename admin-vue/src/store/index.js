import { createStore } from 'vuex'
import state from '@/state'
import getters from '@/getters'
import actions from '@/actions'
import mutations from '@/mutations'
import app from '@/modules/app'
import account from '@/modules/account'
import permission from '@/modules/permission'

export default createStore({
    state: state,
    getters: getters,
    mutations: mutations,
    actions: actions,
    modules: {
        app,
        account,
        permission
    },
})
