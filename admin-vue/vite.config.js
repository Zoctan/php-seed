import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

let config = {
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src'),
      'components': resolve(__dirname, 'src/components'),
    }
  },
  server: {
    port: 8888, // 服务端口号
    open: true, // 服务启动时是否自动打开浏览器
    cors: true, // 允许跨域
  }
}

export default defineConfig(({ command, mode }) => {
  if (command === 'serve') {
  } else if (command === 'build') {
    config.base = '/php-seed/admin-vue/dist/'
  }
  return config
})
