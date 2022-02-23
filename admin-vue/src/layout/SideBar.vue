<template>
  <el-menu mode="vertical" :default-active="$route.path">
    <SideBar-Item :routers="routers" />
  </el-menu>
</template>

<script>
import { mapGetters } from 'vuex'
import SideBarItem from './SideBarItem.vue'

export default {
  name: 'SideBar',
  components: { SideBarItem },
  computed: {
    ...mapGetters(['routers'])
  }
}
</script>

<style lang="less" scoped>
</style>
