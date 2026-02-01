<template>
    <div class="dashboard-wrapper">
        <a-row type="flex" justify="center">
            <a-col :xs="22" :sm="22" :md="22" :lg="20">
                <a-breadcrumb class="modern-breadcrumb mt-30">
                    <a-breadcrumb-item>
                        <router-link :to="{ name: 'front.homepage', params: { warehouse: frontWarehouse.slug } }">
                            <home-outlined /> {{ $t("front.home") }}
                        </router-link>
                    </a-breadcrumb-item>
                    <a-breadcrumb-item>{{ $t("front.dashboard") }}</a-breadcrumb-item>
                </a-breadcrumb>

                <a-row :gutter="[32, 32]" class="mt-30 pb-50">
                    <a-col :xs="24" :lg="6" :xl="5">
                        <div class="sticky-sidebar">
                            <dashboard-sidebar />
                        </div>
                    </a-col>

                    <a-col :xs="24" :lg="18" :xl="19">
                        <div class="dashboard-welcome">
                            <div class="welcome-text">
                                <h1>{{ $t('front.dashboard') }}</h1>
                                <p>Welcome back! Here’s what’s happening with your orders.</p>
                            </div>
                        </div>

                        <a-row :gutter="[20, 20]" class="mt-20">
                            <a-col :xs="12" :sm="12" :md="6">
                                <div class="stat-card total">
                                    <div class="stat-icon"><shopping-cart-outlined /></div>
                                    <div class="stat-info">
                                        <span class="label">{{ $t("front.total_orders") }}</span>
                                        <span class="value">{{ states.totalOrders }}</span>
                                    </div>
                                </div>
                            </a-col>
                            <a-col :xs="12" :sm="12" :md="6">
                                <div class="stat-card pending">
                                    <div class="stat-icon"><sync-outlined /></div>
                                    <div class="stat-info">
                                        <span class="label">{{ $t("front.pending_orders") }}</span>
                                        <span class="value">{{ states.pendingOrders }}</span>
                                    </div>
                                </div>
                            </a-col>
                            <a-col :xs="12" :sm="12" :md="6">
                                <div class="stat-card processing">
                                    <div class="stat-icon"><field-time-outlined /></div>
                                    <div class="stat-info">
                                        <span class="label">Processing</span>
                                        <span class="value">{{ states.processingOrders }}</span>
                                    </div>
                                </div>
                            </a-col>
                            <a-col :xs="12" :sm="12" :md="6">
                                <div class="stat-card completed">
                                    <div class="stat-icon"><check-outlined /></div>
                                    <div class="stat-info">
                                        <span class="label">Completed</span>
                                        <span class="value">{{ states.completedOrders }}</span>
                                    </div>
                                </div>
                            </a-col>
                        </a-row>

                        <a-card :bordered="false" class="recent-orders-card mt-30">
                            <template #title>
                                <div class="card-header-flex">
                                    <span class="title-text">{{ $t('front.recent_orders') }}</span>
                                    <a-button type="link">View All</a-button>
                                </div>
                            </template>
                            <div class="table-container">
                                <OrderTable :data="recentOrders" @reloadOrders="fetchOrders" />
                            </div>
                        </a-card>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </div>
</template>

<style lang="less" scoped>
.dashboard-wrapper {
    background-color: #f8fafc;
    min-height: 100vh;
}

.sticky-sidebar {
    position: sticky;
    top: 100px;
}

.dashboard-welcome {
    margin-bottom: 24px;
    h1 {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }
    p { color: #64748b; font-size: 15px; }
}

/* Custom Stat Cards */
.stat-card {
    background: #fff;
    padding: 20px;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    border: 1px solid #f1f5f9;
    transition: transform 0.3s ease;

    &:hover { transform: translateY(-5px); }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
        .label { font-size: 13px; color: #64748b; font-weight: 500; }
        .value { font-size: 24px; font-weight: 800; color: #1e293b; }
    }

    /* Theme Colors */
    &.total .stat-icon { background: #eff6ff; color: #2874f0; }
    &.pending .stat-icon { background: #fff7ed; color: #f56a00; }
    &.processing .stat-icon { background: #f5f3ff; color: #7265e6; }
    &.completed .stat-icon { background: #f0fdf4; color: #16a34a; }
}

.recent-orders-card {
    border-radius: 20px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);

    .card-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        .title-text { font-weight: 700; font-size: 18px; }
    }
}

.table-container {
    overflow-x: auto;
}

@media (max-width: 768px) {
    .dashboard-welcome h1 { font-size: 22px; }
    .stat-card .value { font-size: 20px; }
}
</style>

<script>
import { defineComponent, ref, onMounted } from "vue";
import {
    ShoppingCartOutlined,
    SyncOutlined,
    FieldTimeOutlined,
    CheckOutlined,
    HomeOutlined
} from "@ant-design/icons-vue";
import DashboardSidebar from "../includes/DashboardSidebar.vue";
import OrderTable from "../components/OrderTable.vue";
import common from "../../../../common/composable/common";
import apiFront from "../../../../common/composable/apiFront";

export default defineComponent({
    components: {
        DashboardSidebar,
        ShoppingCartOutlined,
        SyncOutlined,
        FieldTimeOutlined,
        CheckOutlined,
        HomeOutlined,
        OrderTable,
    },
    setup() {
        const { frontWarehouse } = common();
        const { addEditRequestFront } = apiFront();
        const states = ref({
            totalOrders: 0,
            pendingOrders: 0,
            processingOrders: 0,
            completedOrders: 0,
        });
        const recentOrders = ref([]);

        const fetchOrders = () => {
            addEditRequestFront({
                url: "front/self/dashboard",
                data: {},
                success: (response) => {
                    states.value = response;
                    recentOrders.value = response.recentOrders;
                },
            });
        };

        onMounted(fetchOrders);

        return {
            states,
            recentOrders,
            fetchOrders,
            frontWarehouse,
        };
    },
});
</script>
