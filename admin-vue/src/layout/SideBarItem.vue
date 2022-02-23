<template v-for="router in routers" v-if="!router.hidden">
  <!-- 无子菜单 -->
  <template v-if="!router.dropDown && router.children.length > 0">
    <router-link :to="router.path + '/' + router.children[0].path" :key="router.name">
      <el-menu-item :index="router.name">
        <el-icon v-if="router.icon">{{ router.icon }}</el-icon>
        <span>{{ router.children[0].name }}</span>
      </el-menu-item>
    </router-link>
  </template>
  <!-- 有子菜单 -->
  <template v-if="router.dropDown && router.children.length > 0">
    <el-sub-menu :index="router.name">
      <el-icon v-if="router.icon">{{ router.icon }}</el-icon>
      <span>{{ router.name }}</span>
      <!-- 子菜单 -->
      <template v-for="child in router.children">
        <template v-if="!child.hidden">
          <!-- 多重子菜单：调用自身组件 -->
          <template v-if="child.children && child.children.length > 0">
            <Sidebar-Item :routers="[child]" :key="child.name" />
          </template>
          <!-- 唯一子菜单 -->
          <template v-else>
            <router-link :to="router.path + '/' + child.path" :key="child.name">
              <el-menu-item :index="name">{{ child.name }}</el-menu-item>
            </router-link>
          </template>
        </template>
      </template>
    </el-sub-menu>
  </template>
</template>

<script>
export default {
  name: 'SidebarItem',
  props: {
    routers: {
      type: Array
    }
  }
}
</script>

<style lang="less" scoped>
</style>
