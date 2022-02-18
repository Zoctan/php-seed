<template>
  <div class="error401">
    <van-button @click="onBack" icon="arrow-left">返回</van-button>
    <h1>你没有权限访问页面</h1>
    <img :src="error401Gif" width="313" height="428" alt="Girl has dropped her ice cream." />
  </div>
</template>

<script>
import error401Gif from '@/assets/401.gif'

export default {
  name: 'error401',
  data() {
    return {
      error401Gif
    }
  },
  methods: {
    onBack() {
      if (this.$route.query.noGoBack) {
        this.$router.push({ path: '/dashboard' })
      } else {
        this.$router.go(-1)
      }
    }
  }
}
</script>

<style lang="less" scoped>
.error401 {
}
</style>
