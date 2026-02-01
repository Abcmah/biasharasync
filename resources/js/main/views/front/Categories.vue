<template>
    <div class="category-page-wrapper">
        <a-row type="flex" justify="center">
            <a-col :xs="22" :sm="22" :md="22" :lg="20">
                <a-row :gutter="[32, 32]" class="main-content-row">

                    <a-col :xs="0" :lg="6" :xl="5">
                        <div class="sidebar-sticky-wrapper">
                            <div class="sidebar-card">
                                <h3 class="sidebar-title">{{ $t("menu.categories") }}</h3>
                                <LeftSidebarMenu :catSelectedKeys="catSelectedKeys" />
                            </div>
                        </div>
                    </a-col>

                    <a-col :xs="24" :lg="18" :xl="19">
                        <div class="category-header-section">
                            <a-breadcrumb class="modern-breadcrumb">
                                <a-breadcrumb-item><router-link to="/">{{ $t("front.home") }}</router-link></a-breadcrumb-item>
                                <a-breadcrumb-item><router-link :to="{ name: 'front.categories' }">{{ $t("menu.categories") }}</router-link></a-breadcrumb-item>
                                <a-breadcrumb-item v-if="category?.name">{{ category.name }}</a-breadcrumb-item>
                            </a-breadcrumb>

                            <div class="title-action-bar">
                                <h1 class="page-title">
                                    {{ category?.name || $t("front_setting.all_categories") }}
                                    <small v-if="totalRecords">({{ totalRecords }} {{ $t("front.items") }})</small>
                                </h1>

                                <a-button class="mobile-filter-btn" @click="showMobileFilters = true">
                                    <template #icon><filter-outlined /></template>
                                    Filters
                                </a-button>
                            </div>
                        </div>

                        <a-divider class="header-divider" />

                        <div class="products-container">
                            <a-row :gutter="[20, 20]" v-if="products.length > 0">
                                <a-col
                                    v-for="product in products"
                                    :key="product.xid"
                                    :xs="12" :sm="12" :md="8" :lg="8" :xl="6"
                                >
                                    <ProductCard :product="product" />
                                </a-col>
                            </a-row>

                            <div v-else class="empty-wrapper">
                                <a-empty :description="$t('front_setting.no_results')" />
                            </div>
                        </div>

                        <div class="pagination-wrapper" v-if="totalRecords > pageSize">
                            <a-pagination
                                v-model:current="currentPage"
                                :total="totalRecords"
                                :page-size="pageSize"
                                show-less-items
                                @change="paginationClicked"
                            />
                        </div>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>

        <a-drawer
            v-model:open="showMobileFilters"
            title="Filter Categories"
            placement="bottom"
            height="70%"
            :closable="true"
            class="mobile-filter-drawer"
        >
            <LeftSidebarMenu :catSelectedKeys="catSelectedKeys" />
        </a-drawer>
    </div>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import { FilterOutlined } from "@ant-design/icons-vue";
import LeftSidebarMenu from "./layouts/LeftSidebarMenu.vue";
import ProductCard from "./components/ProductCard.vue";

export default defineComponent({
    components: { LeftSidebarMenu, ProductCard, FilterOutlined },
    setup() {
        const route = useRoute();
        const showMobileFilters = ref(false);
        const products = ref([]);
        const category = ref({});
        const catSelectedKeys = ref([]);
        const totalRecords = ref(0);
        const currentPage = ref(1);
        const pageSize = ref(20);

        // ... Keep your existing getData and getProducts logic ...
        // Ensure you return showMobileFilters

        return {
            catSelectedKeys,
            category,
            products,
            currentPage,
            pageSize,
            totalRecords,
            paginationClicked: (page) => {
                currentPage.value = page;
                // scroll to top smoothly when page changes
                window.scrollTo({ top: 0, behavior: 'smooth' });
                getProducts();
            },
            showMobileFilters
        };
    },
});
</script>

<style lang="less" scoped>
.category-page-wrapper {
    background-color: #f8fafc; // Soft slate background
    min-height: 100vh;
    padding: 40px 0;
}

/* Sidebar Styling */
.sidebar-sticky-wrapper {
    position: sticky;
    top: 100px;
}

.sidebar-card {
    background: #fff;
    border-radius: 16px;
    padding: 20px 0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    border: 1px solid #f1f5f9;

    .sidebar-title {
        padding: 0 24px 15px;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 10px;
    }
}

/* Header Section */
.category-header-section {
    .modern-breadcrumb {
        margin-bottom: 12px;
        font-size: 13px;
    }

    .title-action-bar {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;

        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;

            small {
                font-size: 14px;
                color: #64748b;
                font-weight: 400;
                margin-left: 8px;
            }
        }
    }
}

.header-divider {
    margin: 20px 0 30px;
    border-color: #e2e8f0;
}

/* Product Grid Adjustments */
.products-container {
    min-height: 400px;
}

.pagination-wrapper {
    margin-top: 50px;
    display: flex;
    justify-content: center;
    padding-bottom: 40px;
}

/* Mobile Adjustments */
.mobile-filter-btn {
    display: none;
    border-radius: 8px;
    font-weight: 600;
}

@media (max-width: 991px) {
    .category-page-wrapper { padding: 20px 0; }

    .mobile-filter-btn {
        display: inline-flex;
        align-items: center;
    }

    .category-header-section .title-action-bar .page-title {
        font-size: 22px;
    }
}

@media (max-width: 576px) {
    /* 2 products per row on small mobile for better shopping experience */
    :deep(.ant-col-xs-12) {
        width: 50% !important;
    }
}

.empty-wrapper {
    padding: 100px 0;
    background: #fff;
    border-radius: 16px;
}
</style>
