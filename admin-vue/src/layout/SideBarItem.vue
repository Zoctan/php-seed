<template>
  <template v-for="router in routers" :key="router.name">
    <template v-if="!router.hidden && router.children && router.children.length > 0">
      <!-- 一级菜单 -->
      <template v-if="!router.dropDown">
        <!-- 当前路由路径就是该菜单路径，不重复进入 -->
        <component
          :is="$route.path !== joinPath(router) ? 'router-link' : 'div'"
          :to="joinPath(router)"
        >
          <el-menu-item :index="router.name" :disabled="$route.path === joinPath(router)">
            <template #title>
              <el-icon v-if="router.icon">
                <component :is="router.icon"></component>
              </el-icon>
              <span>{{ router.name }}</span>
            </template>
          </el-menu-item>
        </component>
      </template>
      <!-- 二级以上菜单 -->
      <template v-else>
        <el-sub-menu :index="router.name">
          <template #title>
            <el-icon v-if="router.icon">
              <component :is="router.icon"></component>
            </el-icon>
            <span>{{ router.name }}</span>
          </template>
          <!-- 多重子菜单 -->
          <template v-for="child in router.children" :key="child.name">
            <SideBarItem :routers="[child]" />
          </template>
        </el-sub-menu>
      </template>
    </template>
  </template>
</template>

<script setup>
defineProps({
  routers: {
    type: Array,
    required: true
  },
})

const joinPath = (router) => {
  console.debug(router)
  router.children[0].path == '' ? router.path : `${router.path}/${router.children[0].path}`
}
</script>

<style lang="less" scoped>
</style>
