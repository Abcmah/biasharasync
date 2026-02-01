<template>
    <a-layout v-if="frontWarehouse.online_store_enabled == 1" class="main-store-layout">
        <a-layout-header class="store-header">
            <div class="header-container">
                <div class="header-left">
                    <LeftSidebar ref="mobileMenuRef" />
                    <router-link :to="{ name: 'front.homepage', params: { warehouse: frontWarehouse.slug } }"
                        class="logo-link">
                        <img :src="frontWarehouse.dark_logo_url" class="store-logo" alt="Store Logo" />
                    </router-link>
                </div>

                <div class="header-center" v-if="innerWidth >= 768">

                </div>

                <div class="header-right">

                    <CheckoutDrawer ref="checkoutDrawerRef" @openLoginModal="openLoginModal" />
                    <Login :modalVisible="loginModalVisible" @modalClosed="loginModalClosed" />
                </div>
            </div>
        </a-layout-header>

        <a-layout-content class="store-content">
            <div class="subheader" v-if="innerWidth >= 768">
                <div class="header-container">
                    <div class="subheader-wrapper">
                        <a-dropdown v-if="frontAppSetting?.pages_widget" overlayClassName="modern-dropdown">
                            <div class="category-trigger">
                                <AppstoreOutlined class="category-icon" />
                                <span>Shop Categories</span>
                            </div>
                            <template #overlay>
                                <LeftSidebarMenu />
                            </template>
                        </a-dropdown>

                        <div class="quick-links">
                            <a href="#">New Arrivals</a>
                            <a href="#">Best Sellers</a>
                            <a href="#">Offers</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-view-container">
                <router-view></router-view>
            </div>

            <Footer />
        </a-layout-content>

        <nav class="mobile-bottom-nav" v-if="innerWidth < 768">
            <router-link :to="{ name: 'front.homepage', params: { warehouse: frontWarehouse.slug } }" class="nav-item">
                <HomeOutlined />
                <span>Home</span>
            </router-link>

            <div class="nav-item" @click="showMobileMenu">
                <AppstoreOutlined />
                <span>Categories</span>
            </div>

            <div class="nav-item cart-action">
                <div class="cart-icon-wrapper">
                     <CheckoutDrawer ref="checkoutDrawerRef" @openLoginModal="openLoginModal" />
                </div>
                <span>Cart</span>
            </div>

            <div class="nav-item">
                <Login :modalVisible="loginModalVisible" @modalClosed="loginModalClosed" />
                <span>Profile</span>
            </div>
        </nav>
    </a-layout>

    <div v-else class="no-online-store-container">
        <a-result status="warning" :title="$t('warehouse.no_online_store_exists')" />
    </div>
</template>

<script>
import { defineComponent, ref, onMounted, onUnmounted } from "vue";
import { useStore } from "vuex";
import {
    DownOutlined,
    MenuOutlined,
    HomeOutlined,
    UserOutlined,
    AppstoreOutlined,
    ShoppingCartOutlined
} from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import Footer from "./Footer.vue";
import CheckoutDrawer from "../components/CheckoutDrawer.vue";
import Login from "../components/Login.vue";
import LeftSidebar from "./LeftSidebar.vue";
import LeftSidebarMenu from "./LeftSidebarMenu.vue";

export default defineComponent({
    components: {
        DownOutlined,
        MenuOutlined,
        HomeOutlined,
        AppstoreOutlined,
        UserOutlined,
        Footer,
        CheckoutDrawer,
        Login,
        LeftSidebar,
        ShoppingCartOutlined,
        LeftSidebarMenu,
    },
    setup() {
        const store = useStore();
        const { frontWarehouse, frontAppSetting } = common();
        const loginModalVisible = ref(false);
        const innerWidth = ref(window.innerWidth);

        // Refs to trigger child components
        const checkoutDrawerRef = ref(null);
        const mobileMenuRef = ref(null);

        // Window resize listener
        const updateWidth = () => { innerWidth.value = window.innerWidth; };
        onMounted(() => window.addEventListener('resize', updateWidth));
        onUnmounted(() => window.removeEventListener('resize', updateWidth));

        const openLoginModal = () => { loginModalVisible.value = true; };
        const loginModalClosed = () => { loginModalVisible.value = false; };

        // Logic to trigger drawers from the bottom nav
        const openCart = () => {
            if (checkoutDrawerRef.value) checkoutDrawerRef.value.showDrawer();
        };

        const showMobileMenu = () => {
            if (mobileMenuRef.value) mobileMenuRef.value.showDrawer();
        };

        return {
            frontAppSetting,
            openLoginModal,
            loginModalClosed,
            loginModalVisible,
            frontWarehouse,
            innerWidth,
            checkoutDrawerRef,
            mobileMenuRef,
            openCart,
            showMobileMenu
        };
    },
});
</script>

<style lang="less" scoped>
@primary-color: #2874f0;
@text-main: #1f2937;
@border-color: #f3f4f6;

.main-store-layout {
    background: #ffffff;
    min-height: 100vh;
}

/* --- Header Styles --- */
.store-header {
    background: #ffffff !important;
    height: 70px !important;
    line-height: 70px !important;
    padding: 0 !important;
    position: sticky;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid @border-color;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
}

.header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 100%;
}

.store-logo {
    height: 42px;
    object-fit: contain;
    transition: transform 0.3s ease;

    &:hover {
        transform: scale(1.05);
    }
}

/* --- Subheader Styles --- */
.subheader {
    background: #ffffff;
    border-bottom: 1px solid @border-color;

    .subheader-wrapper {
        display: flex;
        align-items: center;
        gap: 30px;
        height: 50px;
    }
}

.category-trigger {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-weight: 600;
    color: @text-main;

    &:hover {
        color: @primary-color;
    }

    .category-icon {
        font-size: 18px;
    }
}

.quick-links {
    display: flex;
    gap: 25px;

    a {
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.2s;

        &:hover {
            color: @primary-color;
        }
    }
}

/* --- Content Styles --- */
.page-view-container {
    min-height: 60vh;
    background: #fafafa;
    padding: 20px 0 50px 0;
}

/* --- Mobile Bottom Nav --- */
.mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 65px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(15px);
    display: flex;
    justify-content: space-around;
    align-items: center;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    padding-bottom: env(safe-area-inset-bottom);
    z-index: 2000;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08);

    .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #8e8e93;
        font-size: 22px;
        flex: 1;

        span {
            font-size: 10px;
            font-weight: 600;
            margin-top: 2px;
        }

        &.router-link-active {
            color: @primary-color;
        }
    }

    .cart-action {
        position: relative;
        top: -18px;

        .cart-icon-wrapper {
            background: @primary-color;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 8px 15px rgba(40, 116, 240, 0.3);
            border: 5px solid #fff;
        }

        span {
            color: @primary-color;
            margin-top: 4px;
        }
    }
}

.no-online-store-container {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 767px) {
    .store-content {
        padding-bottom: 90px;
    }

    .header-container {
        padding: 0 15px;
    }
}
</style>
