<template @click="toggleSideBar">
  <el-icon v-if="isActive">
    <d-arrow-left />
  </el-icon>
  <el-icon v-else>
    <d-arrow-right />
  </el-icon>
</template>

<script>
export default {
  name: 'SideBarCollapse',
  props: {
    isActive: {
      type: Boolean,
      default: false
    },
    toggleSideBar: {
      type: Function,
      default: null
    }
  }
}
</script>

<style lang="less" scoped>

</style>
