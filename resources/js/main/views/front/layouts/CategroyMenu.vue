<template>
    <template v-for="category in categories" :key="category.xid">
        <a-sub-menu
            v-if="category.children && category.children.length"
            :key="category.xid"
            class="modern-submenu"
        >
            <template #title>
                <span class="menu-item-content">
                    {{ category.name }}
                </span>
            </template>
            <template #icon>
                <div class="category-icon-wrapper">
                    <img :src="category.image_url" class="category-icon-img" />
                </div>
            </template>

            <category-menu :categories="category.children" />
        </a-sub-menu>

        <a-menu-item v-else :key="category.xid" class="modern-menu-item">
            <router-link
                :to="{
                    name: 'front.categories',
                    params: { warehouse: frontWarehouse.slug, slug: [category.slug] },
                }"
                class="menu-link"
            >
                <div class="category-icon-wrapper">
                    <img :src="category.image_url" class="category-icon-img" />
                </div>
                <span class="menu-text">{{ category.name }}</span>
            </router-link>
        </a-menu-item>
    </template>
</template>

<script>
import { defineComponent } from "vue";
import common from "../../../../common/composable/common";

export default defineComponent({
    name: "category-menu",
    props: ["categories"],
    setup() {
        const { frontWarehouse } = common();
        return { frontWarehouse };
    },
});
</script>

<style lang="less" scoped>
/* Remove standard Ant Design padding to make it flush */
:deep(.ant-menu-inline .ant-menu-item),
:deep(.ant-menu-inline .ant-menu-submenu-title) {
    height: 48px !important;
    line-height: 48px !important;
    margin: 4px 8px !important;
    width: calc(100% - 16px) !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;

    &:hover {
        background: #f1f5f9 !important; // Light slate background on hover
        color: #2874f0 !important;
    }
}

.category-icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    margin-right: 8px;
    overflow: hidden;
    border-radius: 4px;
    background: #f8fafc;
    transition: transform 0.2s ease;
}

.category-icon-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.modern-menu-item:hover .category-icon-wrapper,
.modern-submenu:hover .category-icon-wrapper {
    transform: scale(1.1); // Small pop on hover
}

.menu-text {
    font-weight: 500;
    font-size: 14px;
    color: #1e293b;
}

/* Custom indicator for submenus */
:deep(.ant-menu-submenu-arrow) {
    color: #94a3b8 !important;
}
</style>
