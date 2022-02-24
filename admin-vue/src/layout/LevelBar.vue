<template>
  <el-breadcrumb separator-icon="ArrowRight">
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

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'

let levelList = ref([])

// 在 setup 中访问路由和当前路由：https://router.vuejs.org/zh/guide/advanced/composition-api.html
// setup 里面没有访问 this，所以我们不能再直接访问 this.$router 或 this.$route
const route = useRoute()

const getBreadcrumb = () => {
  levelList = route.matched
  console.log(route.matched)
}

onMounted(getBreadcrumb)

// 对路由对象 $route 进行侦听，每次路由都重新生成导航列表
// route 对象是一个响应式对象，所以它的任何属性都可以被监听，但应该避免监听整个 route 对象
watch(() => route.path, () => getBreadcrumb)
</script>

<style lang="less" scoped>
</style>
