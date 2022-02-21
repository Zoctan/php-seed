// https://vuex.vuejs.org/zh/guide/getters.html
export default {
  sidebar: state => state.app.sidebar,

  token: state => state.member.token,
  member: state => state.member.member,

  accessedRouters: state => state.router.accessedRouters,
  accessedAuthRouters: state => state.router.accessedAuthRouters
}
