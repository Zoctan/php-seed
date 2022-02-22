<template>
    <van-overlay :show="show">
        <div class="wrapper">
            <van-loading color="#000000" text-color="#000000" size="24px" vertical>加载中...</van-loading>
        </div>
    </van-overlay>
</template>

<script>
export default {
    name: 'fullscreenProgress',
    setup(props, { expose }) {
        const show = ref(false)
        const toggleShow = () => show = !show

        expose({
            toggleShow
        })

        return {
            show
        }
    }
}
</script>

<style lang="less" scoped>
.wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}
</style>
