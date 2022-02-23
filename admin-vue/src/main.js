import { createApp } from 'vue'
import store from './store'
import router from './router'
import App from './App.vue'

// 创建实例
const app = createApp(App)

// 使用组件
app.use(store)
    .use(router)

app.mount('#app')
