<template>
  <el-menu class mode="horizontal">
    <SideBar-Collapse class :toggleSideBar="toggleSideBar" :isActive="sidebar.opened" />
    <Level-Bar />
    <el-dropdown class>
      <span class="el-dropdown-link">
        {{ name }}
        <el-icon class="el-icon--right">
          <arrow-down />
        </el-icon>
      </span>
      <el-dropdown-menu>
        <el-dropdown-item>
          <router-link to="/member/detail">账户中心</router-link>
        </el-dropdown-item>
        <el-dropdown-item divided @click="logout">注销</el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
  </el-menu>
</template>

<script>
import { computed } from 'vue'
import { mapGetters } from 'vuex'
import LevelBar from './LevelBar.vue'
import SideBarCollapse from './SideBarCollapse.vue'

export default {
  name: 'NavBar',
  components: {
    LevelBar,
    SideBarCollapse
  },
  setup() {
    const { member, sidebar } = computed(() => mapGetters(['member', 'sidebar']))

    const toggleSideBar = () => this.$store.dispatch('toggleSideBar')

    const logout = () => this.$store.dispatch('memberLogout').then(() => location.reload())

    return {
      member, sidebar,
      toggleSideBar,
      logout
    }
  }
}
</script>

<style lang="less" scoped>
</style>
