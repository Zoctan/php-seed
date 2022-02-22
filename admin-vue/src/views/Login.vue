<template>
  <div class="login-container">
    <van-form @submit="onSubmit">
      <h3 class="title">后台登录</h3>
      <van-cell-group inset>
        <van-field
          v-model="username"
          left-icon="user-circle-o"
          name="用户名"
          label="用户名"
          placeholder="请填写用户名"
          maxlength="20"
          show-word-limit
          :rules="[{ required: true, validator: usernameValidator, message: '用户名长度必须在3或以上' }]"
        />
        <van-field
          v-model="password"
          type="password"
          left-icon="closed-eye"
          name="密码"
          label="密码"
          maxlength="20"
          show-word-limit
          placeholder="请填写密码"
          :rules="[{ required: true, validator: passwordValidator, message: '密码长度必须在6或以上' }]"
        />
      </van-cell-group>
      <div style="margin: 16px;">
        <van-button round block type="primary" native-type="submit">提交</van-button>
      </div>
    </van-form>
  </div>
</template>

<script>
import { ref } from 'vue'
import { Toast } from 'vant'

export default {
  name: 'login',
  setup() {
    const username = ref('admin')
    const password = ref('admin123')

    const usernameValidator = (value) => value.length >= 3

    const passwordValidator = (value) => value.length >= 6

    const onSubmit = (values) => {
      console.log('submit', values)
      return
      const account = {}

      account.password = this.loginForm.password
      this.loading = true
      this.$store.dispatch('login', account).then(() => {
        this.loading = false
        this.$router.push({ path: '/home' })
      })
    }

    return {
      username,
      password,
      usernameValidator,
      passwordValidator,
      onSubmit,
    }
  },
  data() {
    return {

    }
  },
  methods: {

  }
}
</script>

<style lang="less">
.login-container {
}
</style>
