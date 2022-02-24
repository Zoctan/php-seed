import { createApp } from 'vue'
import store from './store'
import router from './router'
import App from './App.vue'
import * as ElIcons from '@element-plus/icons-vue'

// 创建实例
const app = createApp(App)

// 使用组件
app.use(store)
    .use(router)
// 图标组件
for (const name in ElIcons) {
    app.component(name, ElIcons[name])
}

app.mount('#app')
