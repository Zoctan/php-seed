<template>
  <el-menu class mode="horizontal">
    <SideBarCollapse />
    <LevelBar />

    <!-- <el-dropdown class>
      <span class="el-dropdown-link">
        {{ member.name }}
        <el-icon class="el-icon--right">
          <arrow-down />
        </el-icon>
      </span>
      <el-dropdown-menu>
        <el-dropdown-item :icon="avatar">
          <router-link to="/member/detail">账户中心</router-link>
        </el-dropdown-item>
        <el-dropdown-item divided :icon="back">
          <span @click="logout">注销</span>
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>-->
  </el-menu>
</template>

<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import LevelBar from './LevelBar.vue'
import SideBarCollapse from './SideBarCollapse.vue'

const store = useStore()
const member = computed(() => store.getters.member)

const logout = () => store.dispatch('memberLogout').then(() => location.reload())
</script>

<style lang="less" scoped>
</style>
