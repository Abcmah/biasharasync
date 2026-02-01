<template>
    <MenuOutlined
        v-if="innerWidth < 768"
        class="mobile-menu-trigger"
        @click="openLeftSidebar"
    />

    <a-drawer
        v-model:open="visible"
        width="280"
        placement="left"
        :closable="false"
        class="modern-nav-drawer"
        @close="drawerClosed"
    >
        <template #title>
            <div class="drawer-branding">
                <img :src="frontWarehouse.dark_logo_url" alt="Logo" />
            </div>
        </template>

        <template #extra>
            <div class="drawer-close-wrapper" @click="closeLeftSidebar">
                <close-outlined />
            </div>
        </template>

        <div class="drawer-content-scroll">
            <div class="nav-section">
                <h4 class="nav-section-title">{{ $t("front.pages") }}</h4>
                <a-menu
                    v-model:openKeys="openKeys"
                    v-model:selectedKeys="selectedKeys"
                    mode="inline"
                    class="modern-side-menu"
                >
                    <a-menu-item
                        v-for="(item, index) in frontAppSetting.pages_widget"
                        :key="index"
                        class="modern-side-item"
                    >
                        <router-link :to="item.value">
                            <span class="dot-indicator"></span>
                            {{ item.title }}
                        </router-link>
                    </a-menu-item>
                </a-menu>
            </div>

            <div class="drawer-footer-actions" v-if="innerWidth < 768">
                <a-divider />
                <div class="footer-promo">
                    <p>Free shipping on orders over $50!</p>
                </div>
            </div>
        </div>
    </a-drawer>
</template>

<style lang="less" scoped>
/* Hamburger Trigger Styles */
.mobile-menu-trigger {
    font-size: 22px;
    color: #1f2937; // Darker color for modern look
    cursor: pointer;
    padding: 8px;
    transition: all 0.2s ease;

    &:hover {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 8px;
    }
}

/* Drawer Global Styles */
.modern-nav-drawer {
    :deep(.ant-drawer-header) {
        background: #ffffff !important;
        border-bottom: 1px solid #f3f4f6;
        padding: 20px 24px;
    }

    .drawer-branding img {
        height: 32px;
        object-fit: contain;
    }

    .drawer-close-wrapper {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
        border-radius: 50%;
        color: #6b7280;
        cursor: pointer;
        transition: 0.2s;

        &:hover {
            background: #f3f4f6;
            color: #111827;
        }
    }
}

/* Navigation Content */
.nav-section {
    padding-top: 20px;

    .nav-section-title {
        padding: 0 24px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #9ca3af;
        margin-bottom: 12px;
        font-weight: 700;
    }
}

/* Customizing the Ant Menu */
.modern-side-menu {
    border-inline-end: none !important;

    :deep(.ant-menu-item) {
        height: 48px !important;
        line-height: 48px !important;
        margin: 4px 12px !important;
        width: calc(100% - 24px) !important;
        border-radius: 10px !important;
        color: #4b5563;
        font-weight: 500;

        .dot-indicator {
            display: inline-block;
            width: 6px;
            height: 6px;
            background: #d1d5db;
            border-radius: 50%;
            margin-right: 12px;
            transition: 0.2s;
        }

        &:hover {
            background: #f1f5f9 !important;
            color: #2874f0 !important;
            .dot-indicator { background: #2874f0; transform: scale(1.2); }
        }

        &.ant-menu-item-selected {
            background: #eef2ff !important;
            color: #2874f0 !important;
            .dot-indicator { background: #2874f0; }
        }
    }
}

.footer-promo {
    padding: 20px 24px;
    p {
        font-size: 13px;
        color: #6b7280;
        background: #f9fafb;
        padding: 12px;
        border-radius: 8px;
        text-align: center;
    }
}
</style>
