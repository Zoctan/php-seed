<template>
  <el-menu
    text-color="#fff"
    active-text-color="#ffd04b"
    background-color="#545c64"
    mode="vertical"
    :default-active="$router.path"
  >
    <SideBarItem :routers="routers" />
  </el-menu>
</template>

<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import SideBarItem from './SideBarItem.vue'

const store = useStore()

const routers = computed(() => store.getters.routers)
</script>

<style lang="less" scoped>
</style>
