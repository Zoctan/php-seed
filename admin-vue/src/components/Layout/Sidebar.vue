<template>
  <!-- http://element-cn.eleme.io/#/zh-CN/component/menu -->
  <el-menu mode="vertical" :default-active="$route.path">
    <sidebar-item :routes="permissionRouters" />
  </el-menu>
</template>

<script>
import { mapGetters } from 'vuex'
import SidebarItem from './SidebarItem'

export default {
  name: 'sidebar',
  components: { SidebarItem },
  computed: {
    ...mapGetters(['permissionRouters'])
  }
}
</script>

<style lang="less" scoped>
.el-menu {
  min-height: 100%;
}
</style>
