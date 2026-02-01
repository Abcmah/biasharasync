<template>
    <div class="category-menu-wrapper">
        <a-menu
            v-model:selectedKeys="selectedKeys"
            v-model:openKeys="openKeys"
            mode="inline"
            class="modern-category-menu"
        >
            <template v-for="category in allCategories" :key="category.xid">
                <a-sub-menu
                    v-if="category.children && category.children.length"
                    :key="category.xid"
                    class="modern-submenu"
                >
                    <template #icon>
                        <div class="menu-icon-container">
                            <img :src="category.image_url" class="menu-thumb" />
                        </div>
                    </template>
                    <template #title>
                        <span class="menu-title-text">{{ category.name }}</span>
                    </template>
                    <CategoryMenu :categories="category.children" />
                </a-sub-menu>

                <a-menu-item v-else :key="category.xid" class="modern-menu-item">
                    <template #icon>
                        <div class="menu-icon-container">
                            <img :src="category.image_url" class="menu-thumb" />
                        </div>
                    </template>
                    <router-link :to="{ name: 'front.categories', params: { slug: [category.slug] } }">
                        <span class="menu-title-text">{{ category.name }}</span>
                    </router-link>
                </a-menu-item>
            </template>

            <a-divider style="margin: 8px 0" />
            <a-menu-item key="view-all-categories" class="view-all-item">
                <template #icon>
                    <div class="menu-icon-container view-all-icon">
                        <AppstoreOutlined />
                    </div>
                </template>
                <router-link :to="{ name: 'front.categories', params: { warehouse: frontWarehouse.slug } }">
                    <span class="menu-title-text explore-text">Explore All Categories</span>
                </router-link>
            </a-menu-item>
        </a-menu>
    </div>
</template>

<script>
import { defineComponent, onMounted, ref, watch } from "vue";
import { AppstoreOutlined } from "@ant-design/icons-vue";
import common from "../../../../common/composable/common";
import CategoryMenu from "./CategroyMenu.vue";

export default defineComponent({
    props: ["catSelectedKeys", "catOpenKeys"],
    components: { CategoryMenu, AppstoreOutlined },
    setup(props) {
        const { frontWarehouse } = common();
        const allCategories = ref([]);
        const selectedKeys = ref([]);
        const openKeys = ref([]);

        const getCategories = async () => {
            try {
                const response = await axiosFront.post("front/categories");
                const listArray = response.data.categories;
                const topLevel = [];
                const map = {};

                listArray.forEach(node => map[node.xid] = { ...node, children: [] });
                listArray.forEach(node => {
                    if (node.x_parent_id && map[node.x_parent_id]) {
                        map[node.x_parent_id].children.push(map[node.xid]);
                    } else {
                        topLevel.push(map[node.xid]);
                    }
                });
                allCategories.value = topLevel;
            } catch (error) {
                console.error("Error fetching categories:", error);
            }
        };

        onMounted(() => {
            selectedKeys.value = props.catSelectedKeys || [];
            getCategories();
        });

        watch(() => props.catSelectedKeys, (newVal) => {
            selectedKeys.value = newVal || [];
        });

        return {
            selectedKeys,
            openKeys,
            allCategories,
            frontWarehouse
        };
    },
});
</script>

<style lang="less" scoped>
/* Reuse the previous styles, adding the 'View All' specifics below */
.modern-category-menu {
    border-inline-end: none !important;

    .view-all-item {
        background: transparent !important;
        margin-top: 10px !important;

        .view-all-icon {
            background: #f8fafc;
            color: #2874f0;
            font-size: 16px;
            border: 1px dashed #cbd5e1;
        }

        .explore-text {
            color: #2874f0;
            font-weight: 600;
        }

        &:hover {
            .view-all-icon {
                background: #2874f0;
                color: #fff;
                border-style: solid;
            }
        }
    }
}

.menu-icon-container {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    overflow: hidden;
    vertical-align: middle;
    margin-right: 12px;
}

.menu-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

:deep(.ant-menu-item), :deep(.ant-menu-submenu-title) {
    border-radius: 12px !important;
    margin: 4px 12px !important;
    width: calc(100% - 24px) !important;
}

:deep(.ant-menu-item-selected) {
    background-color: #eef2ff !important;
    color: #2874f0 !important;
}
</style>
