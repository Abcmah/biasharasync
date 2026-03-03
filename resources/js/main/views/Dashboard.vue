<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.dashboard`)" style="padding: 0px" />
        </template>
    </AdminPageHeader>

    <div class="dashboard-page-content-container dashboard-modern">

        <!-- Date Filter Section -->
        <div class="filter-section fade-in">
            <a-row :gutter="[16, 16]">
                <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
                    <DateRangePicker
                        ref="serachDateRangePicker"
                        @dateTimeChanged="
                            (changedDateTime) => (filters.dates = changedDateTime)
                        "
                    />
                </a-col>
            </a-row>
        </div>

        <!-- Stats Cards Section -->
        <div class="stats-section fade-in-up">
            <a-row :gutter="[20, 20]">
                <a-col :xs="24" :sm="12" :md="12" :lg="6" :xl="6" v-for="(stat, index) in statsCards" :key="index">
                    <div class="stat-card-wrapper" :style="{ animationDelay: `${index * 0.1}s` }">
                        <StateWidget>
                            <template #image>
                                <component :is="stat.icon" class="stat-icon" />
                            </template>
                            <template #description>
                                <h2 v-if="responseData.stateData" class="stat-value">
                                    {{
                                        formatAmountCurrency(
                                            responseData.stateData[stat.dataKey]
                                        )
                                    }}
                                </h2>
                                <p class="stat-label">{{ $t(stat.label) }}</p>
                            </template>
                        </StateWidget>
                    </div>
                </a-col>
            </a-row>
        </div>

        <!-- Charts Section -->
        <a-row :gutter="[20, 20]" class="charts-section fade-in-up">
            <a-col :xs="24" :sm="24" :md="24" :lg="6" :xl="6">
                <a-card
                    :title="$t('dashboard.top_selling_product')"
                    class="dashboard-card hover-lift"
                    :hoverable="true"
                >
                    <TopProducts :data="responseData" />
                </a-card>
            </a-col>
            <a-col :xs="24" :sm="24" :md="24" :lg="18" :xl="18">
                <a-card
                    :title="$t('dashboard.sales_purchases')"
                    class="dashboard-card hover-lift"
                    :hoverable="true"
                >
                    <PurchaseSales :data="responseData" />
                    <template
                        v-if="
                            permsArray.includes('sales_view') ||
                            permsArray.includes('admin')
                        "
                        #extra
                    >
                        <a-button
                            class="view-all-btn"
                            type="link"
                            @click="
                                $router.push({
                                    name: 'admin.stock.sales.index',
                                })
                            "
                        >
                            {{ $t("common.view_all") }}
                            <DoubleRightOutlined />
                        </a-button>
                    </template>
                </a-card>
            </a-col>
        </a-row>

        <!-- Stock History Section -->
        <a-row
            :gutter="[20, 20]"
            class="stock-history-section fade-in-up"
            v-if="
                (permsArray.includes('purchases_view') ||
                    permsArray.includes('sales_view') ||
                    permsArray.includes('purchase_returns_view') ||
                    permsArray.includes('sales_returns_view') ||
                    permsArray.includes('admin')) &&
                activeOrderType != ''
            "
        >
            <a-col :span="24">
                <a-card
                    :title="$t('dashboard.recent_stock_history')"
                    :bodyStyle="{ paddingTop: '0px' }"
                    class="dashboard-card hover-lift"
                    :hoverable="true"
                >
                    <template #extra>
                        <a-tabs v-model:activeKey="activeOrderType" class="modern-tabs">
                            <a-tab-pane
                                v-if="
                                    permsArray.includes('sales_view') ||
                                    permsArray.includes('admin')
                                "
                                key="sales"
                                :tab="$t('menu.sales')"
                            />
                            <a-tab-pane
                                v-if="
                                    permsArray.includes('purchases_view') ||
                                    permsArray.includes('admin')
                                "
                                key="purchases"
                                :tab="$t('menu.purchases')"
                            />
                            <a-tab-pane
                                v-if="
                                    permsArray.includes('purchase_returns_view') ||
                                    permsArray.includes('admin')
                                "
                                key="purchase-returns"
                                :tab="$t('menu.purchase_returns')"
                            />
                            <a-tab-pane
                                v-if="
                                    permsArray.includes('sales_returns_view') ||
                                    permsArray.includes('admin')
                                "
                                key="sales-returns"
                                :tab="$t('menu.sales_returns')"
                            />
                        </a-tabs>
                    </template>
                    <a-row :gutter="[20, 20]">
                        <a-col
                            :xs="24"
                            :sm="24"
                            :md="24"
                            :lg="6"
                            :xl="6"
                            class="stats-sidebar"
                        >
                            <a-row
                                v-if="responseData.stockHistoryStatsData"
                                class="stock-history-stats"
                            >
                                <a-col :span="24" class="sales stat-item">
                                    <a-statistic
                                        :title="$t('dashboard.total_sales_items')"
                                        :value="
                                            formatQuantity(
                                                responseData.stockHistoryStatsData
                                                    .totalSales
                                            )
                                        "
                                    />
                                </a-col>
                                <a-col :span="24" class="sales-returns stat-item">
                                    <a-statistic
                                        :title="$t('dashboard.total_sales_returns_items')"
                                        :value="
                                            formatQuantity(
                                                responseData.stockHistoryStatsData
                                                    .totalSalesReturn
                                            )
                                        "
                                    />
                                </a-col>
                                <a-col :span="24" class="purchases stat-item">
                                    <a-statistic
                                        :title="$t('dashboard.total_purchases_items')"
                                        :value="
                                            formatQuantity(
                                                responseData.stockHistoryStatsData
                                                    .totalPurchases
                                            )
                                        "
                                    />
                                </a-col>

                                <a-col :span="24" class="purchase-returns stat-item">
                                    <a-statistic
                                        :title="
                                            $t('dashboard.total_purchase_returns_items')
                                        "
                                        :value="
                                            formatQuantity(
                                                responseData.stockHistoryStatsData
                                                    .totalPurchaseReturn
                                            )
                                        "
                                    />
                                </a-col>
                            </a-row>
                        </a-col>
                        <a-col :xs="24" :sm="24" :md="24" :lg="18" :xl="18">
                            <OrderTable
                                :orderType="activeOrderType"
                                :filters="filters"
                                :perPageItems="5"
                            />
                        </a-col>
                    </a-row>
                </a-card>
            </a-col>
        </a-row>

        <!-- Payments Chart Section -->
        <a-row :gutter="[20, 20]" class="payments-section fade-in-up">
            <a-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                <a-card
                    :title="$t('payments.payments')"
                    class="dashboard-card hover-lift"
                    :hoverable="true"
                >
                    <PaymentsChart :data="responseData" />
                    <template
                        v-if="
                            permsArray.includes('order_payments_view') ||
                            permsArray.includes('admin')
                        "
                        #extra
                    >
                        <a-button
                            class="view-all-btn"
                            type="link"
                            @click="
                                $router.push({ name: 'admin.reports.payments.index' })
                            "
                        >
                            {{ $t("common.view_all") }}
                            <DoubleRightOutlined />
                        </a-button>
                    </template>
                </a-card>
            </a-col>
        </a-row>

        <!-- Bottom Section: Stock Alerts & Top Customers -->
        <a-row :gutter="[20, 20]" class="bottom-section fade-in-up">
            <a-col :xs="24" :sm="24" :md="24" :lg="16" :xl="16">
                <a-card
                    :title="$t('menu.stock_alert')"
                    :bodyStyle="{ padding: '0px' }"
                    class="dashboard-card hover-lift"
                    :hoverable="true"
                    v-if="responseData && responseData.stockAlerts"
                >
                    <a-table
                        :columns="stockQuantityColumns"
                        :row-key="(record) => record.xid"
                        :data-source="responseData.stockAlerts"
                        :pagination="false"
                        class="modern-table"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'current_stock'">
                                {{ `${record.current_stock} ${record.short_name}` }}
                            </template>
                            <template v-if="column.dataIndex === 'stock_quantitiy_alert'">
                                {{
                                    `${record.stock_quantitiy_alert} ${record.short_name}`
                                }}
                            </template>
                        </template>
                    </a-table>
                    <template
                        v-if="
                            permsArray.includes('warehouses_view') ||
                            permsArray.includes('admin')
                        "
                        #extra
                    >
                        <a-button
                            class="view-all-btn"
                            type="link"
                            @click="$router.push({ name: 'admin.reports.stock.index' })"
                        >
                            {{ $t("common.view_all") }}
                            <DoubleRightOutlined />
                        </a-button>
                    </template>
                </a-card>
            </a-col>
            <a-col :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
                <a-card
                    :title="$t('dashboard.top_customers')"
                    :bodyStyle="{ padding: '0px' }"
                    class="dashboard-card hover-lift"
                    :hoverable="true"
                    v-if="responseData && responseData.topCustomers"
                >
                    <a-table
                        :columns="topCustomerColumns"
                        :row-key="(record) => record.customer_id"
                        :data-source="responseData.topCustomers"
                        :pagination="false"
                        class="modern-table"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex == 'customer_id'">
                                <user-info :user="record.customer" />
                            </template>
                            <template v-if="column.dataIndex == 'total_amount'">
                                {{ formatAmountCurrency(record.total_amount) }} <br />
                                <span class="text-muted">{{ $t("dashboard.total_sales") }}: {{ record.total_sales }}</span>
                            </template>
                        </template>
                    </a-table>
                    <template
                        v-if="
                            permsArray.includes('users_view') ||
                            permsArray.includes('admin')
                        "
                        #extra
                    >
                        <a-button
                            class="view-all-btn"
                            type="link"
                            @click="$router.push({ name: 'admin.reports.users.index' })"
                        >
                            {{ $t("common.view_all") }}
                            <DoubleRightOutlined />
                        </a-button>
                    </template>
                </a-card>
            </a-col>
        </a-row>
    </div>
</template>

<script>
import { ref, onMounted, reactive, toRef, watch } from "vue";
import {
    EyeOutlined,
    ArrowUpOutlined,
    ArrowDownOutlined,
    LineChartOutlined,
    BankOutlined,
    ShoppingOutlined,
    TagOutlined,
    DoubleRightOutlined,
} from "@ant-design/icons-vue";
import { notification } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";
import common from "../../common/composable/common";
import TopProducts from "../components/charts/dashboard/TopProducts.vue";
import PurchaseSales from "../components/charts/dashboard/PurchaseSales.vue";
import PaymentsChart from "../components/charts/dashboard/PaymentsChart.vue";
import StateWidget from "../../common/components/common/card/StateWidget.vue";
import Tiimeline from "../components/stock-history/Tiimeline.vue";
import OrderTable from "../components/order/OrderTable.vue";
import UserInfo from "../../common/components/user/UserInfo.vue";
import DateRangePicker from "../../common/components/common/calendar/DateRangePicker.vue";
import AdminPageHeader from "../../common/layouts/AdminPageHeader.vue";

export default {
    components: {
        EyeOutlined,
        ArrowUpOutlined,
        ArrowDownOutlined,
        DoubleRightOutlined,
        StateWidget,
        TopProducts,
        PurchaseSales,
        PaymentsChart,
        OrderTable,
        UserInfo,
        Tiimeline,
        LineChartOutlined,
        BankOutlined,
        ShoppingOutlined,
        TagOutlined,
        DateRangePicker,
        AdminPageHeader,
    },
    setup() {
        const { t } = useI18n();
        const {
            formatQuantity,
            formatAmountCurrency,
            appSetting,
            user,
            permsArray,
            selectedWarehouse,
        } = common();
        const activeOrderType = ref("");
        const filters = reactive({
            dates: [],
        });
        const responseData = ref([]);
        const route = useRoute();

        // Stats cards configuration
        const statsCards = [
            {
                icon: LineChartOutlined,
                dataKey: 'totalSales',
                label: 'dashboard.total_sales',
            },
            {
                icon: ShoppingOutlined,
                dataKey: 'totalExpenses',
                label: 'dashboard.total_expenses',
            },
            {
                icon: TagOutlined,
                dataKey: 'paymentSent',
                label: 'dashboard.payment_sent',
            },
            {
                icon: BankOutlined,
                dataKey: 'paymentReceived',
                label: 'dashboard.payment_received',
            },
        ];

        const stockQuantityColumns = [
            {
                title: t("product.product"),
                dataIndex: "product_name",
            },
            {
                title: t("product.quantity"),
                dataIndex: "current_stock",
            },
            {
                title: t("product.quantitiy_alert"),
                dataIndex: "stock_quantitiy_alert",
            },
        ];

        const topCustomerColumns = [
            {
                title: t("stock.customer"),
                dataIndex: "customer_id",
            },
            {
                title: t("payments.total_amount"),
                dataIndex: "total_amount",
            },
        ];

        onMounted(() => {
            const dashboardPromise = axiosAdmin.post("dashboard", filters);

            if (permsArray.value.includes("purchases_view")) {
                activeOrderType.value = "purchases";
            } else if (permsArray.value.includes("purchase_returns_view")) {
                activeOrderType.value = "purchase-returns";
            } else if (permsArray.value.includes("sales_returns_view")) {
                activeOrderType.value = "sales-returns";
            } else {
                activeOrderType.value = "sales";
            }

            // Message showing when comes from login page
            if (route.params && route.params.success) {
                notification.success({
                    message: t("common.welcome_back", [user.value.name]),
                    description: t("messages.login_success_dashboard"),
                    placement: "topRight",
                });
            }

            Promise.all([dashboardPromise]).then(([dashboardResponse]) => {
                responseData.value = dashboardResponse.data;
            });
        });

        watch([filters, selectedWarehouse], (newVal, oldVal) => {
            axiosAdmin.post("dashboard", filters).then((response) => {
                responseData.value = response.data;
            });
        });

        return {
            filters,
            activeOrderType,
            responseData,
            statsCards,
            stockQuantityColumns,
            topCustomerColumns,
            formatQuantity,
            formatAmountCurrency,
            permsArray,
            appSetting,
        };
    },
};
</script>

<style lang="less">
// Modern Dashboard Styles - Relaxing UX/UI
.dashboard-modern {
    padding-bottom: 40px;

    // Smooth scrolling
    scroll-behavior: smooth;
}

// Animation keyframes
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

// Fade-in animations
.fade-in {
    animation: fadeIn 0.6s ease-out;
}

.fade-in-up {
    animation: fadeInUp 0.8s ease-out;
}

// Filter Section
.filter-section {
    margin-bottom: 24px;
    margin-top: 24px;
}

// Stats Section with modern cards
.stats-section {
    margin-bottom: 28px;
}

.stat-card-wrapper {
    animation: slideInRight 0.6s ease-out both;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

    &:hover {
        transform: translateY(-4px);
    }
}

.stat-icon {
    color: #fff;
    font-size: 26px;
    transition: transform 0.3s ease;
}

.stat-card-wrapper:hover .stat-icon {
    transform: scale(1.1) rotate(5deg);
}

.stat-value {
    font-size: 24px;
    font-weight: 600;
    margin: 8px 0 4px 0;
    color: #1a1a1a;
    letter-spacing: -0.5px;
}

.stat-label {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
}

// Dashboard Cards with soft shadows
.dashboard-card {
    border-radius: 16px;
    border: 1px solid #f0f0f5;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;

    &.hover-lift:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
        border-color: #e8e8f0;
    }

    .ant-card-head {
        border-bottom: 1px solid #f5f5f8;
        padding: 20px 24px;
        background: linear-gradient(180deg, #fafafa 0%, #ffffff 100%);

        .ant-card-head-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            padding: 0;
            margin-top: 0;
        }
    }

    .ant-card-body {
        padding: 24px;
    }
}

// View All Button styling
.view-all-btn {
    color: #4f46e5;
    font-weight: 500;
    transition: all 0.2s ease;
    padding: 8px 12px;
    border-radius: 8px;

    &:hover {
        background: #eef2ff;
        color: #4338ca;
        transform: translateX(4px);
    }
}

// Charts Section
.charts-section {
    margin-bottom: 28px;
}

// Stock History Section
.stock-history-section {
    margin-bottom: 28px;
}

.stats-sidebar {
    @media (max-width: 991px) {
        margin-bottom: 20px;
    }
}

.stock-history-stats {
    display: flex;
    flex-direction: column;
    gap: 16px;

    @media (min-width: 992px) {
        padding-right: 24px;
        border-right: 1px solid #f0f0f5;
    }

    .stat-item {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px;
        border-radius: 14px;
        transition: all 0.3s ease;
        border: 1px solid transparent;

        &:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            border-color: #e2e8f0;
        }

        .ant-statistic-title {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .ant-statistic-content {
            font-size: 22px;
            font-weight: 600;
            color: #1e293b;
        }
    }

    .sales {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border-color: #a7f3d0;

        .ant-statistic-content {
            color: #065f46;
        }
    }

    .sales-returns {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border-color: #fecaca;

        .ant-statistic-content {
            color: #991b1b;
        }
    }

    .purchases {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-color: #bfdbfe;

        .ant-statistic-content {
            color: #1e40af;
        }
    }

    .purchase-returns {
        background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%);
        border-color: #fde68a;

        .ant-statistic-content {
            color: #92400e;
        }
    }
}

// Modern Tabs
.modern-tabs {
    .ant-tabs-tab {
        font-weight: 500;
        color: #6b7280;
        transition: all 0.2s ease;

        &:hover {
            color: #4f46e5;
        }

        &.ant-tabs-tab-active {
            color: #4f46e5;
        }
    }

    .ant-tabs-ink-bar {
        background: #4f46e5;
        height: 3px;
        border-radius: 3px 3px 0 0;
    }
}

// Payments Section
.payments-section {
    margin-bottom: 28px;
}

// Bottom Section
.bottom-section {
    margin-bottom: 28px;
}

// Modern Table styling
.modern-table {
    .ant-table {
        border-radius: 0 0 16px 16px;

        thead > tr > th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            padding: 16px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody > tr {
            transition: all 0.2s ease;

            &:hover {
                background: #f9fafb;

                td {
                    background: transparent !important;
                }
            }

            td {
                padding: 16px;
                border-bottom: 1px solid #f3f4f6;
            }
        }
    }
}

// Text utilities
.text-muted {
    color: #9ca3af;
    font-size: 12px;
}

// Card header adjustments
.ant-card-extra,
.ant-card-head-title {
    padding: 0;
}

// Responsive adjustments
@media (max-width: 768px) {
    .dashboard-card {
        margin-bottom: 16px;

        .ant-card-head {
            padding: 16px;
        }

        .ant-card-body {
            padding: 16px;
        }
    }

    .stat-value {
        font-size: 20px;
    }

    .stock-history-stats {
        padding-right: 0;
        border-right: none;
        gap: 12px;
    }
}

@media (max-width: 576px) {
    .filter-section {
        margin-top: 16px;
        margin-bottom: 16px;
    }

    .stats-section {
        margin-bottom: 20px;
    }

    .stat-card-wrapper {
        margin-bottom: 12px;
    }

    .dashboard-card {
        border-radius: 12px;
    }
}

// Loading state with skeleton (optional enhancement)
.skeleton-loading {
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

// Smooth transitions for all interactive elements
* {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

// Custom scrollbar for modern look
.dashboard-page-content-container::-webkit-scrollbar {
    width: 8px;
}

.dashboard-page-content-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.dashboard-page-content-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;

    &:hover {
        background: #94a3b8;
    }
}
</style>
