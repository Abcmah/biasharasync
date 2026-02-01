<template>
    <div class="sidebar-nav-container">
        <a-menu
            v-model:selectedKeys="selectedKeys"
            class="dashboard-sidebar-menu"
            :mode="menuMode"
        >
            <a-menu-item key="dashboard">
                <template #icon><home-outlined /></template>
                <router-link :to="{ name: 'front.dashboard' }">
                    {{ $t("front.dashboard") }}
                </router-link>
            </a-menu-item>

            <a-menu-item key="orders">
                <template #icon><shopping-outlined /></template>
                <router-link :to="{ name: 'front.orders' }">
                    {{ $t("front.my_orders") }}
                </router-link>
            </a-menu-item>

            <a-menu-item key="profile">
                <template #icon><setting-outlined /></template>
                <router-link :to="{ name: 'front.profile' }">
                    {{ $t("front.my_profile") }}
                </router-link>
            </a-menu-item>

            <a-menu-item @click="logout" key="logout" class="logout-item">
                <template #icon><logout-outlined /></template>
                <span>{{ $t("front.logout") }}</span>
            </a-menu-item>
        </a-menu>
    </div>
</template>

<script>
import { defineComponent, ref, onMounted, watch, onUnmounted } from "vue";
import {
    HomeOutlined,
    ShoppingOutlined,
    SettingOutlined,
    LogoutOutlined,
} from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";

export default defineComponent({
    components: { HomeOutlined, ShoppingOutlined, SettingOutlined, LogoutOutlined },
    setup() {
        const route = useRoute();
        const store = useStore();
        const selectedKeys = ref([]);
        const menuMode = ref("inline");

        const updateMenuMode = () => {
            menuMode.value = window.innerWidth < 992 ? "horizontal" : "inline";
        };

        const syncMenu = (currentRoute) => {
            const menuKey = typeof currentRoute.meta.menuKey === "function"
                ? currentRoute.meta.menuKey(currentRoute)
                : currentRoute.meta.menuKey;

            if (menuKey) {
                selectedKeys.value = [menuKey.replace("-", "_")];
            }
        };

        onMounted(() => {
            updateMenuMode();
            window.addEventListener("resize", updateMenuMode);
            syncMenu(route);
        });

        onUnmounted(() => {
            window.removeEventListener("resize", updateMenuMode);
        });

        watch(route, (newVal) => syncMenu(newVal));

        const logout = () => store.dispatch("front/logout");

        return { selectedKeys, logout, menuMode };
    },
});
</script>

<style lang="less" scoped>
.sidebar-nav-container {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.dashboard-sidebar-menu {
    border-right: none !important;
    padding: 20px 10px;

    :deep(.ant-menu-item) {
        height: 50px;
        line-height: 50px;
        border-radius: 10px;
        margin-bottom: 8px;
        font-weight: 500;

        &.ant-menu-item-selected {
            background-color: #eff6ff !important;
            color: #2874f0;
            &::after { display: none; } // Remove the default Ant line
        }

        &:hover { color: #2874f0; }
    }

    .logout-item {
        margin-top: 20px;
        color: #ef4444;
        &:hover { color: #dc2626 !important; background: #fef2f2 !important; }
    }
}

/* Mobile Responsiveness */
@media (max-width: 991px) {
    .sidebar-nav-container {
        margin-bottom: 20px;
        border-radius: 12px;
        box-shadow: none;
        background: transparent;
    }

    .dashboard-sidebar-menu {
        display: flex;
        justify-content: space-between;
        padding: 5px;
        background: #fff;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: none; // Firefox

        &::-webkit-scrollbar { display: none; } // Chrome/Safari

        :deep(.ant-menu-item) {
            margin-bottom: 0;
            flex: 1;
            text-align: center;
            padding: 0 15px !important;
            font-size: 13px;

            .anticon { margin-right: 5px; }
        }

        .logout-item { margin-top: 0; }
    }
}
@media (max-width: 991px) {
    .dashboard-sidebar-menu {
        display: flex;
        width: 100%;
        padding: 5px;
        background: #fff;

        // This ensures items shrink to fit the screen exactly
        :deep(.ant-menu-item) {
            flex: 1;
            margin: 0 !important;
            padding: 0 5px !important;
            text-align: center;
            display: flex;
            flex-direction: column; // Stack icon over text
            align-items: center;
            justify-content: center;
            height: 60px;
            font-size: 11px; // Smaller text for mobile

            .anticon {
                margin: 0;
                font-size: 18px;
            }

            // Hide text if screen is very small (optional)
            span {
                line-height: 1.2;
                margin-top: 4px;
            }
        }
    }
}
</style>
