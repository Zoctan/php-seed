<template>
  <div class="app-main">
    <transition name="fade" mode="out-in">
      <router-view :key="key" />
    </transition>
  </div>
</template>

<script>
export default {
  name: 'AppMain',
  computed: {
    key() {
      return this.$route.name
    }
  }
}
</script>
