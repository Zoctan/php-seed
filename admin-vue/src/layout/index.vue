<template>
  <div class="app-wrapper" :class="{ hideSidebar: !sidebar.opened }">
    <div class="sidebar-wrapper">
      <Side-Bar class="sidebar-container" />
    </div>
    <div class="main-container">
      <Nav-Bar />
      <App-Main />
    </div>
  </div>
</template>

<script>
import NavBar from './NavBar.vue'
import SideBar from './SideBar.vue'
import AppMain from './AppMain.vue'

export default {
  name: 'AppLayout',
  components: {
    NavBar,
    SideBar,
    AppMain
  },
  computed: {
    sidebar() {
      return this.$store.getters.sidebar
    }
  }
}
</script>

<style lang="less" scoped>
.app-wrapper {
  position: relative;
  height: 100%;
  width: 100%;
  &.hideSidebar {
    .sidebar-wrapper {
      transform: translate(-140px, 0);
      .sidebar-container {
        transform: translate(132px, 0);
      }
      &:hover {
        transform: translate(0, 0);
        .sidebar-container {
          transform: translate(0, 0);
        }
      }
    }
    .main-container {
      margin-left: 40px;
    }
  }
  .sidebar-wrapper {
    width: 180px;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 1001;
    overflow: hidden;
    transition: all 0.28s ease-out;
  }
  .sidebar-container {
    transition: all 0.28s ease-out;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: -17px;
    overflow-y: scroll;
  }
  .main-container {
    min-height: 100%;
    transition: all 0.28s ease-out;
    margin-left: 180px;
  }
}
</style>
