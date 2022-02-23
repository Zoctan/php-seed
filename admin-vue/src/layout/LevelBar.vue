<template>
  <el-breadcrumb :separator-icon="ArrowRight">
    <template v-for="(item, index) in levelList" :key="item.name">
      <el-breadcrumb-item>
        <template
          v-if="item.redirect === 'noRedirect' || index === levelList.length - 1"
        >{{ item.name }}</template>
        <template v-else>
          <router-link :to="item.redirect || item.path">{{ item.name }}</router-link>
        </template>
      </el-breadcrumb-item>
    </template>
  </el-breadcrumb>
</template>

<script>
import { ref, onMounted, watch } from 'vue'

export default {
  name: 'LevelBar',
  setup() {
    const levelList = ref([])

    const getBreadcrumb = () => {
      let routerNameList = this.$route.matched.filter(router => router.name)
      const firstRouterName = routerNameList[0]
      if (firstRouterName && (firstRouterName.name !== 'dashboard' || firstRouterName.path !== '')) {
        routerNameList.push({ name: '/dashboard', path: '/' })
      }
      this.levelList = routerNameList
    }

    onMounted(getBreadcrumb)

    // 对路由对象 $route 进行侦听，每次路由都重新生成导航列表
    watch(this.$route, getBreadcrumb)

    return {
      levelList,
    }
  }
}
</script>

<style lang="less" scoped>
</style>
