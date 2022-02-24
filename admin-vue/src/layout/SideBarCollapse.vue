<template>
  <div @click="toggleSideBar">
    <el-icon v-if="sidebar.opened" color="#409EFF">
      <arrow-left-bold />
    </el-icon>
    <el-icon v-else color="#409EFF">
      <arrow-right-bold />
    </el-icon>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()

const sidebar = computed(() => store.getters.sidebar)

const toggleSideBar = () => store.dispatch('toggleSideBar')
</script>

<style lang="less" scoped>
</style>
