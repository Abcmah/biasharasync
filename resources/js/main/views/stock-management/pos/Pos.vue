<template>
    <div class="pos-page" :class="{ 'pos-fullscreen': isFullScreen }">
        <!-- Header -->
        <div class="pos-header-container">
            <div class="header-left">
                <a-button type="text" class="back-btn" @click="() => $router.go(-1)">
                    <template #icon>
                        <ArrowLeftOutlined />
                    </template>
                </a-button>
                <div class="header-titles">
                    <h1 class="pos-title">{{ $t('menu.pos') }}</h1>
                    <a-breadcrumb separator="·" class="modern-breadcrumb">
                        <a-breadcrumb-item>{{ $t('menu.dashboard') }}</a-breadcrumb-item>
                        <a-breadcrumb-item>{{ $t('menu.stock_management') }}</a-breadcrumb-item>
                        <a-breadcrumb-item class="active">{{ $t('menu.pos') }}</a-breadcrumb-item>
                    </a-breadcrumb>
                </div>
            </div>
            <div class="header-center">
                <div class="pos-clock">
                    <span class="time">{{ currentTime }}</span>
                    <span class="date">{{ currentDate }}</span>
                </div>
            </div>
            <div class="header-right">
                <a-space :size="12">
                    <a-tooltip title="Keyboard Shortcuts (?)">
                        <a-button @click="helpVisible = true" class="action-icon-btn">
                            <template #icon>
                                <QuestionCircleOutlined />
                            </template>
                        </a-button>
                    </a-tooltip>
                    <a-tooltip :title="isFullScreen ? 'Exit Fullscreen' : 'Enter Fullscreen'">
                        <a-button @click="toggleFullScreen" class="action-icon-btn">
                            <template #icon>
                                <FullscreenExitOutlined v-if="isFullScreen" />
                                <FullscreenOutlined v-else />
                            </template>
                        </a-button>
                    </a-tooltip>
                    <a-divider type="vertical" />
                    <a-button type="primary" danger ghost
                        @click="() => $router.push({ name: 'admin.dashboard.index' })">
                        <template #icon>
                            <LogoutOutlined />
                        </template>
                        <span class="exit-label">Exit POS</span>
                    </a-button>
                </a-space>
            </div>
        </div>

        <!-- Main Content -->
        <a-form layout="vertical" class="pos-form-wrapper">
            <div class="pos-body">
                <!-- Left Panel: Cart -->
                <div class="pos-left-panel">
                    <!-- Customer & Search -->
                    <a-card class="pos-filters-card" :bordered="false">
                        <div class="filter-row">
                            <a-select v-model:value="formData.user_id"
                                :placeholder="$t('user.walk_in_customer')" style="width: 100%"
                                optionFilterProp="title" show-search>
                                <a-select-option v-for="customer in customers" :key="customer.xid"
                                    :title="customer.name" :value="customer.xid">
                                    {{ customer.name }}
                                    <span v-if="customer.phone && customer.phone !== ''">
                                        <br />{{ customer.phone }}
                                    </span>
                                </a-select-option>
                            </a-select>
                            <CustomerAddButton @onAddSuccess="customerAdded" />
                        </div>
                        <div class="filter-row mt-8">
                            <a-select ref="productSearchInput" :value="null"
                                :searchValue="orderSearchTerm" show-search :filter-option="false"
                                :placeholder="$t('product.search_scan_product')"
                                style="width: 100%"
                                :not-found-content="productFetching ? undefined : null"
                                @search="onProductSearch" option-label-prop="label"
                                @focus="products = []" @select="searchValueSelected"
                                @inputKeyDown="inputValueChanged">
                                <template #suffixIcon>
                                    <SearchOutlined />
                                </template>
                                <template v-if="productFetching" #notFoundContent>
                                    <a-spin size="small" />
                                </template>
                                <a-select-option v-for="product in products" :key="product.xid"
                                    :value="product.xid" :label="product.name" :product="product">
                                    {{ product.name }}
                                </a-select-option>
                            </a-select>
                        </div>
                    </a-card>

                    <!-- Cart Items Table -->
                    <div class="pos-cart-table">
                        <a-table :row-key="(record) => record.xid" :dataSource="selectedProducts"
                            :columns="orderItemColumns" :pagination="false" size="small"
                            :locale="{ emptyText: $t('common.no_data') }">
                            <template #bodyCell="{ column, record }">
                                <template v-if="column.dataIndex === 'name'">
                                    <div class="cart-product-name">{{ record.name }}</div>
                                    <a-typography-text type="secondary" class="cart-stock-info">
                                        {{ $t('product.avl_qty') }}
                                        {{ `${record.stock_quantity}${record.unit_short_name}` }}
                                    </a-typography-text>
                                </template>
                                <template v-if="column.dataIndex === 'unit_quantity'">
                                    <a-input-number v-model:value="record.quantity" :min="0"
                                        size="small" style="width: 70px"
                                        @change="quantityChanged(record)" />
                                </template>
                                <template v-if="column.dataIndex === 'subtotal'">
                                    {{ formatAmountCurrency(record.subtotal) }}
                                </template>
                                <template v-if="column.dataIndex === 'action'">
                                    <a-space :size="4">
                                        <a-button type="text" size="small" @click="editItem(record)">
                                            <template #icon>
                                                <EditOutlined />
                                            </template>
                                        </a-button>
                                        <a-button type="text" size="small" danger
                                            @click="showDeleteConfirm(record)">
                                            <template #icon>
                                                <DeleteOutlined />
                                            </template>
                                        </a-button>
                                    </a-space>
                                </template>
                            </template>
                        </a-table>
                    </div>

                    <!-- Footer: Tax/Discount/Shipping + Actions -->
                    <div class="pos-footer-bar">
                        <div class="footer-controls">
                            <a-row :gutter="12">
                                <a-col :xs="24" :sm="8">
                                    <a-form-item :label="$t('stock.order_tax')" class="compact-form-item">
                                        <a-select v-model:value="formData.tax_id"
                                            :placeholder="$t('common.select_default_text', [$t('stock.order_tax')])"
                                            :allowClear="true" style="width: 100%" @change="taxChanged"
                                            size="small">
                                            <a-select-option v-for="tax in taxes" :key="tax.xid"
                                                :value="tax.xid" :tax="tax">
                                                {{ tax.name }} ({{ tax.rate }}%)
                                            </a-select-option>
                                        </a-select>
                                    </a-form-item>
                                </a-col>
                                <a-col :xs="24" :sm="8">
                                    <a-form-item :label="$t('stock.discount')" class="compact-form-item">
                                        <a-input-group compact>
                                            <a-select v-model:value="formData.discount_type"
                                                @change="recalculateFinalTotal" style="width: 30%"
                                                size="small">
                                                <a-select-option value="percentage">%</a-select-option>
                                                <a-select-option value="fixed">
                                                    {{ appSetting.currency.symbol }}
                                                </a-select-option>
                                            </a-select>
                                            <a-input-number v-model:value="formData.discount_value"
                                                :placeholder="$t('stock.discount')"
                                                @change="recalculateFinalTotal" :min="0"
                                                style="width: 70%" size="small" />
                                        </a-input-group>
                                    </a-form-item>
                                </a-col>
                                <a-col :xs="24" :sm="8">
                                    <a-form-item :label="$t('stock.shipping')" class="compact-form-item">
                                        <a-input-number v-model:value="formData.shipping"
                                            :placeholder="$t('stock.shipping')"
                                            @change="recalculateFinalTotal" :min="0"
                                            style="width: 100%" size="small">
                                            <template #addonBefore>
                                                {{ appSetting.currency.symbol }}
                                            </template>
                                        </a-input-number>
                                    </a-form-item>
                                </a-col>
                            </a-row>
                        </div>

                        <div class="footer-top-actions">
                            <div class="left-meta">
                                <span class="meta-item">
                                    <strong>{{ $t('product.tax') }}:</strong>
                                    {{ formatAmountCurrency(formData.tax_amount) }}
                                </span>
                                <a-divider type="vertical" />
                                <span class="meta-item">
                                    <strong>{{ $t('product.discount') }}:</strong>
                                    {{ formatAmountCurrency(formData.discount) }}
                                </span>
                            </div>
                            <a-space :size="8" wrap>
                                <a-button class="btn-quotation" size="small" @click="createQuotation">
                                    <template #icon><FileTextOutlined /></template>
                                    {{ $t('stock.quotation') || 'Quotation' }}
                                    <span class="shortcut-badge">F7</span>
                                </a-button>
                                <a-button class="btn-suspend" size="small" @click="suspendSale">
                                    <template #icon><PauseCircleOutlined /></template>
                                    Suspend
                                    <span class="shortcut-badge">F8</span>
                                </a-button>
                                <a-button class="btn-restore" size="small" @click="openSuspendedSales">
                                    <template #icon><HistoryOutlined /></template>
                                    Restore
                                    <span class="shortcut-badge">F9</span>
                                </a-button>
                            </a-space>
                        </div>

                        <div class="footer-main-row">
                            <div class="grand-total-section">
                                <span class="total-label">{{ $t('stock.grand_total') }}</span>
                                <span class="total-amount">{{ formatAmountCurrency(formData.subtotal) }}</span>
                            </div>
                            <div class="checkout-section">
                                <a-space :size="12">
                                    <a-button @click="resetPos" danger type="text" size="large">
                                        {{ $t('stock.reset') }}
                                    </a-button>
                                    <a-button type="primary" size="large" class="pay-now-btn"
                                        :disabled="formData.subtotal <= 0 || !formData.user_id"
                                        @click="payNow">
                                        <template #icon><CreditCardOutlined /></template>
                                        <div class="btn-content">
                                            <span class="btn-text">{{ $t('stock.pay_now') }}</span>
                                            <span class="btn-shortcut">F10</span>
                                        </div>
                                    </a-button>
                                </a-space>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Product Grid -->
                <div class="pos-right-panel">
                    <PosLayout1 v-if="postLayout == 1" :brands="brands" :categories="categories"
                        :formData="formData" @changed="reFetchProducts" />
                    <PosLayout2 v-else :brands="brands" :categories="categories"
                        :formData="formData" @changed="reFetchProducts" />

                    <!-- Skeleton Loading -->
                    <div v-if="productsLoading" class="product-grid">
                        <div v-for="n in 8" :key="'skeleton-' + n" class="product-skeleton-card">
                            <a-skeleton-image class="skeleton-img" />
                            <div class="skeleton-body">
                                <a-skeleton :paragraph="{ rows: 1 }" :title="{ width: '80%' }"
                                    active />
                            </div>
                        </div>
                    </div>

                    <!-- Product Grid -->
                    <div v-else-if="productLists.length > 0" class="product-grid">
                        <div v-for="item in productLists" :key="item.xid" class="product-grid-item"
                            @click="selectSaleProduct(item)">
                            <ProductCardNew :product="item" />
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="product-empty-state">
                        <a-empty :description="$t('stock.no_product_found')" />
                    </div>
                </div>
            </div>
        </a-form>

        <!-- Edit Item Modal -->
        <a-modal :open="addEditVisible" :closable="false" :centered="true" :title="addEditPageTitle"
            @ok="onAddEditSubmit">
            <a-form layout="vertical">
                <a-row :gutter="16">
                    <a-col :span="24">
                        <a-form-item :label="$t('product.unit_price')" name="unit_price"
                            :help="addEditRules.unit_price ? addEditRules.unit_price.message : null"
                            :validateStatus="addEditRules.unit_price ? 'error' : null">
                            <a-input-number v-model:value="addEditFormData.unit_price"
                                :placeholder="$t('common.placeholder_default_text', [$t('product.unit_price')])"
                                :min="0" style="width: 100%">
                                <template #addonBefore>{{ appSetting.currency.symbol }}</template>
                            </a-input-number>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="16">
                    <a-col :span="24">
                        <a-form-item :label="$t('product.discount')" name="discount_rate"
                            :help="addEditRules.discount_rate ? addEditRules.discount_rate.message : null"
                            :validateStatus="addEditRules.discount_rate ? 'error' : null">
                            <a-input-number v-model:value="addEditFormData.discount_rate"
                                :placeholder="$t('common.placeholder_default_text', [$t('product.discount')])"
                                :min="0" style="width: 100%">
                                <template #addonAfter>%</template>
                            </a-input-number>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="16">
                    <a-col :span="24">
                        <a-form-item :label="$t('product.tax')" name="tax_id"
                            :help="addEditRules.tax_id ? addEditRules.tax_id.message : null"
                            :validateStatus="addEditRules.tax_id ? 'error' : null">
                            <a-select v-model:value="addEditFormData.tax_id"
                                :placeholder="$t('common.select_default_text', [$t('product.tax')])"
                                :allowClear="true">
                                <a-select-option v-for="tax in taxes" :key="tax.xid" :value="tax.xid">
                                    {{ tax.name }} ({{ tax.rate }}%)
                                </a-select-option>
                            </a-select>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="16">
                    <a-col :span="24">
                        <a-form-item :label="$t('product.tax_type')" name="tax_type"
                            :help="addEditRules.tax_type ? addEditRules.tax_type.message : null"
                            :validateStatus="addEditRules.tax_type ? 'error' : null">
                            <a-select v-model:value="addEditFormData.tax_type"
                                :placeholder="$t('common.select_default_text', [$t('product.tax_type')])"
                                :allowClear="true">
                                <a-select-option v-for="taxType in taxTypes" :key="taxType.key"
                                    :value="taxType.key">
                                    {{ taxType.value }}
                                </a-select-option>
                            </a-select>
                        </a-form-item>
                    </a-col>
                </a-row>
            </a-form>
            <template #footer>
                <a-button key="submit" type="primary" :loading="addEditFormSubmitting"
                    @click="onAddEditSubmit">
                    <template #icon><SaveOutlined /></template>
                    {{ $t('common.update') }}
                </a-button>
                <a-button key="back" @click="onAddEditClose">
                    {{ $t('common.cancel') }}
                </a-button>
            </template>
        </a-modal>

        <!-- Pay Now Modal -->
        <PayNow :visible="payNowVisible" @closed="payNowClosed" @success="payNowSuccess"
            :data="formData" :selectedProducts="selectedProducts" />

        <!-- Invoice Modal -->
        <InvoiceModal :visible="printInvoiceModalVisible" :order="printInvoiceOrder"
            @closed="printInvoiceModalVisible = false" />

        <!-- Suspended Sales Modal -->
        <a-modal v-model:open="showSuspendModal" title="Suspended Sales" width="700px" :footer="null">
            <a-table :columns="suspendColumns" :dataSource="suspendedSales" rowKey="id" size="small">
                <template #bodyCell="{ column, record }">
                    <template v-if="column.dataIndex === 'subtotal'">
                        {{ formatAmountCurrency(record.subtotal) }}
                    </template>
                    <template v-if="column.dataIndex === 'action'">
                        <a-space>
                            <a-button type="primary" size="small" @click="restoreSale(record)">
                                Restore
                            </a-button>
                            <a-button danger size="small" @click="deleteSuspendedSale(record.id)">
                                Delete
                            </a-button>
                        </a-space>
                    </template>
                </template>
            </a-table>
        </a-modal>

        <!-- Help Modal -->
        <ShortcutModal @close-help-modal="closeHelpModal" :help-visible="helpVisible" />
    </div>
</template>

<script>
import { ref, onMounted, reactive, toRefs, nextTick, onBeforeUnmount } from "vue";
import {
    ArrowLeftOutlined, FullscreenOutlined, FullscreenExitOutlined,
    FileTextOutlined, PauseCircleOutlined, HistoryOutlined, CreditCardOutlined,
    EditOutlined, DeleteOutlined, SearchOutlined, SaveOutlined,
    QuestionCircleOutlined, LogoutOutlined,
} from "@ant-design/icons-vue";
import { debounce } from "lodash-es";
import { useI18n } from "vue-i18n";
import { message } from "ant-design-vue";
import { includes, find } from "lodash-es";
import common from "../../../../common/composable/common";
import fields from "./fields";
import ProductCardNew from "../../../../common/components/product/ProductCardNew.vue";
import PayNow from "./PayNow.vue";
import CustomerAddButton from "../../users/CustomerAddButton.vue";
import InvoiceModal from "./Invoice.vue";
import PosLayout1 from "./PosLayout1.vue";
import PosLayout2 from "./PosLayout2.vue";
import ShortcutModal from "./components/ShortcutModal.vue";
import dayjs from 'dayjs';

export default {
    components: {
        ArrowLeftOutlined, FullscreenOutlined, FullscreenExitOutlined,
        FileTextOutlined, PauseCircleOutlined, HistoryOutlined, CreditCardOutlined,
        EditOutlined, DeleteOutlined, SearchOutlined, SaveOutlined,
        QuestionCircleOutlined, LogoutOutlined,
        PosLayout1, PosLayout2,
        ProductCardNew, PayNow, CustomerAddButton, InvoiceModal, ShortcutModal,
    },
    setup() {
        const {
            taxes, customers, brands, categories, productLists,
            orderItemColumns, formData, customerUrl, getPreFetchData, posDefaultCustomer,
        } = fields();

        const selectedProducts = ref([]);
        const productSearchInput = ref(null);
        const selectedProductIds = ref([]);
        const removedOrderItemsIds = ref([]);
        const productsLoading = ref(true);
        const helpVisible = ref(false);
        const postLayout = ref(1);

        const state = reactive({
            orderSearchTerm: undefined,
            productFetching: false,
            products: [],
        });

        const {
            formatAmount, formatAmountCurrency, appSetting, taxTypes, permsArray,
        } = common();
        const { t } = useI18n();

        // AddEdit
        const addEditVisible = ref(false);
        const addEditFormSubmitting = ref(false);
        const addEditFormData = ref({});
        const addEditRules = ref([]);
        const addEditPageTitle = ref("");

        // Pay Now
        const payNowVisible = ref(false);
        const printInvoiceModalVisible = ref(false);
        const printInvoiceOrder = ref({});

        // Suspend
        const suspendedSales = ref([]);
        const showSuspendModal = ref(false);
        const suspendTokenCounter = ref(1);
        let clockInterval = null;
        let beepAudio = null;

        const suspendColumns = [
            { title: "Token", dataIndex: "token", key: "token" },
            { title: "Subtotal", dataIndex: "subtotal", key: "subtotal" },
            { title: "Date", dataIndex: "created_at", key: "created_at" },
            { title: "Action", dataIndex: "action", key: "action" },
        ];

        // Clock
        const isFullScreen = ref(false);
        const currentTime = ref(dayjs().format('HH:mm:ss'));
        const currentDate = ref(dayjs().format('ddd, D MMM YYYY'));

        const startClock = () => {
            clockInterval = setInterval(() => {
                currentTime.value = dayjs().format('HH:mm:ss');
            }, 1000);
        };

        const toggleFullScreen = () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
                isFullScreen.value = true;
            } else {
                document.exitFullscreen();
                isFullScreen.value = false;
            }
        };

        // Lifecycle
        onMounted(() => {
            productsLoading.value = true;
            getPreFetchData().finally(() => {
                productsLoading.value = false;
            });
            loadSuspendedSales();
            startClock();
            window.addEventListener("keydown", handleKeyboardShortcuts);
        });

        onBeforeUnmount(() => {
            window.removeEventListener("keydown", handleKeyboardShortcuts);
            if (clockInterval) clearInterval(clockInterval);
        });

        // Product fetching
        const reFetchProducts = () => {
            productsLoading.value = true;
            axiosAdmin
                .post("pos/products", {
                    brand_id: formData.value.brand_id,
                    category_id: formData.value.category_id,
                })
                .then((productResponse) => {
                    productLists.value = productResponse.data.products;
                })
                .finally(() => {
                    productsLoading.value = false;
                });
        };

        const fetchProducts = debounce((value) => {
            fetchAllSearchedProduct(value);
        }, 300);

        const onProductSearch = (searchedValue) => {
            state.orderSearchTerm = searchedValue;
            fetchProducts(searchedValue);
        };

        const fetchAllSearchedProduct = (value) => {
            state.products = [];
            if (value !== "") {
                state.productFetching = true;
                axiosAdmin
                    .post("search-product", {
                        order_type: "sales",
                        search_term: value,
                    })
                    .then((response) => {
                        if (response.data.length === 1) {
                            searchValueSelected("", { product: response.data[0] });
                        } else {
                            state.products = response.data;
                        }
                        state.productFetching = false;
                    })
                    .catch(() => {
                        state.productFetching = false;
                    });
            }
        };

        const inputValueChanged = (keydownEvent) => {
            nextTick(() => {
                if (keydownEvent.keyCode === 13) {
                    fetchAllSearchedProduct(keydownEvent.target.value);
                }
            });
        };

        const searchValueSelected = (value, option) => {
            selectSaleProduct(option.product);
        };

        // Beep sound (reuse single Audio instance)
        const playBeep = () => {
            try {
                if (!beepAudio && appSetting.value.beep_audio_url) {
                    beepAudio = new Audio(appSetting.value.beep_audio_url);
                }
                if (beepAudio) {
                    beepAudio.currentTime = 0;
                    beepAudio.play().catch(() => {});
                }
            } catch (e) {
                // Audio playback not critical
            }
        };

        const selectSaleProduct = (newProduct) => {
            if (!includes(selectedProductIds.value, newProduct.xid)) {
                selectedProductIds.value.push(newProduct.xid);
                selectedProducts.value.push({
                    ...newProduct,
                    sn: selectedProducts.value.length + 1,
                    unit_price: formatAmount(newProduct.unit_price),
                    tax_amount: formatAmount(newProduct.tax_amount),
                    subtotal: formatAmount(newProduct.subtotal),
                });
                state.orderSearchTerm = undefined;
                state.products = [];
                recalculateFinalTotal();
                playBeep();
            } else {
                const existingProduct = find(selectedProducts.value, ["xid", newProduct.xid]);

                if (existingProduct && existingProduct.quantity < existingProduct.stock_quantity) {
                    existingProduct.quantity += 1;
                    state.orderSearchTerm = undefined;
                    state.products = [];
                    quantityChanged(existingProduct);
                    playBeep();
                } else {
                    state.orderSearchTerm = undefined;
                    state.products = [];
                    message.error(t("common.out_of_stock"));
                }
            }
        };

        const recalculateValues = (product) => {
            let quantityValue = parseFloat(product.quantity);
            const maxQuantity = parseFloat(product.stock_quantity);
            const unitPrice = parseFloat(product.unit_price);

            quantityValue = quantityValue > maxQuantity ? maxQuantity : quantityValue;

            const discountRate = product.discount_rate;
            const totalDiscount = discountRate > 0 ? (discountRate / 100) * unitPrice : 0;
            const totalPriceAfterDiscount = unitPrice - totalDiscount;

            let taxAmount = 0;
            let subtotal = totalPriceAfterDiscount;
            let singleUnitPrice = unitPrice;

            if (product.tax_rate > 0) {
                if (product.tax_type === "inclusive") {
                    singleUnitPrice = (totalPriceAfterDiscount * 100) / (100 + product.tax_rate);
                    taxAmount = singleUnitPrice * (product.tax_rate / 100);
                } else {
                    taxAmount = totalPriceAfterDiscount * (product.tax_rate / 100);
                    subtotal = totalPriceAfterDiscount + taxAmount;
                    singleUnitPrice = totalPriceAfterDiscount;
                }
            }

            return {
                ...product,
                total_discount: totalDiscount * quantityValue,
                subtotal: subtotal * quantityValue,
                quantity: quantityValue,
                total_tax: taxAmount * quantityValue,
                max_quantity: maxQuantity,
                single_unit_price: singleUnitPrice,
            };
        };

        const quantityChanged = (record) => {
            selectedProducts.value = selectedProducts.value.map((p) =>
                p.xid === record.xid ? recalculateValues(record) : p
            );
            recalculateFinalTotal();
        };

        const recalculateFinalTotal = () => {
            let total = selectedProducts.value.reduce((sum, p) => sum + p.subtotal, 0);

            let discountAmount = 0;
            if (formData.value.discount_type === "percentage") {
                discountAmount = formData.value.discount_value
                    ? (parseFloat(formData.value.discount_value) * total) / 100
                    : 0;
            } else if (formData.value.discount_type === "fixed") {
                discountAmount = formData.value.discount_value
                    ? parseFloat(formData.value.discount_value)
                    : 0;
            }

            const taxRate = formData.value.tax_rate ? parseFloat(formData.value.tax_rate) : 0;
            total = total - discountAmount;
            const tax = total * (taxRate / 100);
            total = total + parseFloat(formData.value.shipping || 0);

            formData.value.subtotal = formatAmount(total + tax);
            formData.value.tax_amount = formatAmount(tax);
            formData.value.discount = discountAmount;
        };

        const showDeleteConfirm = (product) => {
            const newResults = [];
            let counter = 1;
            selectedProducts.value.forEach((selectedProduct) => {
                if (selectedProduct.item_id != null && selectedProduct.xid === product.xid) {
                    removedOrderItemsIds.value.push(selectedProduct.item_id);
                }
                if (selectedProduct.xid !== product.xid) {
                    newResults.push({
                        ...selectedProduct,
                        sn: counter,
                        single_unit_price: formatAmount(selectedProduct.single_unit_price),
                        tax_amount: formatAmount(selectedProduct.tax_amount),
                        subtotal: formatAmount(selectedProduct.subtotal),
                    });
                    counter++;
                }
            });
            selectedProducts.value = newResults;
            selectedProductIds.value = selectedProductIds.value.filter((id) => id !== product.xid);
            recalculateFinalTotal();
        };

        const taxChanged = (value, option) => {
            formData.value.tax_rate = value === undefined ? 0 : option.tax.rate;
            recalculateFinalTotal();
        };

        const editItem = (product) => {
            addEditFormData.value = {
                id: product.xid,
                discount_rate: product.discount_rate,
                unit_price: product.unit_price,
                tax_id: product.x_tax_id,
                tax_type: product.tax_type == null ? undefined : product.tax_type,
            };
            addEditVisible.value = true;
            addEditPageTitle.value = product.name;
        };

        const payNow = () => { payNowVisible.value = true; };
        const payNowClosed = () => { payNowVisible.value = false; };

        const resetPos = () => {
            selectedProducts.value = [];
            selectedProductIds.value = [];
            formData.value = {
                ...formData.value,
                tax_id: undefined,
                category_id: undefined,
                brand_id: undefined,
                tax_rate: 0,
                tax_amount: 0,
                discount_value: 0,
                discount: 0,
                shipping: 0,
                subtotal: 0,
            };
            recalculateFinalTotal();
        };

        const onAddEditSubmit = () => {
            const record = selectedProducts.value.find(
                (p) => p.xid === addEditFormData.value.id
            );
            if (!record) return;

            const selectedTax = taxes.value.find(
                (tax) => tax.xid === addEditFormData.value.tax_id
            );
            const taxType = addEditFormData.value.tax_type || "exclusive";

            const newData = {
                ...record,
                discount_rate: parseFloat(addEditFormData.value.discount_rate),
                unit_price: parseFloat(addEditFormData.value.unit_price),
                tax_id: addEditFormData.value.tax_id,
                tax_rate: selectedTax ? selectedTax.rate : 0,
                tax_type: taxType,
            };
            quantityChanged(newData);
            onAddEditClose();
        };

        const onAddEditClose = () => {
            addEditFormData.value = {};
            addEditVisible.value = false;
        };

        const customerAdded = () => {
            axiosAdmin.get(customerUrl).then((response) => {
                customers.value = response.data;
            });
        };

        const payNowSuccess = (invoiceOrder) => {
            resetPos();
            const walkInCustomerId = posDefaultCustomer.value?.xid || undefined;
            formData.value = { ...formData.value, user_id: walkInCustomerId };
            reFetchProducts();
            payNowVisible.value = false;
            printInvoiceOrder.value = invoiceOrder;
            printInvoiceModalVisible.value = true;
        };

        // Suspend & Restore
        const loadSuspendedSales = () => {
            const data = localStorage.getItem("suspendedSales");
            suspendedSales.value = data ? JSON.parse(data) : [];
            const token = localStorage.getItem("suspendTokenCounter");
            suspendTokenCounter.value = token ? parseInt(token) : 1;
        };

        const suspendSale = () => {
            if (selectedProducts.value.length === 0) {
                message.warning("Cart is empty");
                return;
            }
            const suspended = {
                id: `SUS-${Date.now()}`,
                token: suspendTokenCounter.value,
                customer_id: formData.value.user_id,
                items: JSON.parse(JSON.stringify(selectedProducts.value)),
                formData: JSON.parse(JSON.stringify(formData.value)),
                subtotal: formData.value.subtotal,
                created_at: new Date().toLocaleString(),
            };
            suspendedSales.value.push(suspended);
            localStorage.setItem("suspendedSales", JSON.stringify(suspendedSales.value));
            suspendTokenCounter.value++;
            localStorage.setItem("suspendTokenCounter", suspendTokenCounter.value);
            resetPos();
            message.success(`Sale suspended — Token #${suspended.token}`);
        };

        const openSuspendedSales = () => {
            loadSuspendedSales();
            showSuspendModal.value = true;
        };

        const restoreSale = (record) => {
            selectedProducts.value = JSON.parse(JSON.stringify(record.items));
            formData.value = JSON.parse(JSON.stringify(record.formData));

            // Rebuild selectedProductIds from restored items
            selectedProductIds.value = selectedProducts.value.map((p) => p.xid);

            suspendedSales.value = suspendedSales.value.filter((s) => s.id !== record.id);
            localStorage.setItem("suspendedSales", JSON.stringify(suspendedSales.value));
            showSuspendModal.value = false;
            recalculateFinalTotal();
            message.success("Sale restored successfully");
        };

        const deleteSuspendedSale = (id) => {
            suspendedSales.value = suspendedSales.value.filter((s) => s.id !== id);
            localStorage.setItem("suspendedSales", JSON.stringify(suspendedSales.value));
            message.success("Suspended sale deleted");
        };

        const closeHelpModal = () => { helpVisible.value = false; };

        // Quotation (stub - implement as needed)
        const createQuotation = () => {
            if (selectedProducts.value.length === 0) {
                message.warning("Cart is empty");
                return;
            }
            message.info("Quotation feature coming soon");
        };

        // Keyboard shortcuts
        let lastSpacePress = 0;
        const handleKeyboardShortcuts = (e) => {
            const targetedKeys = ['F1', 'F5', 'F7', 'F8', 'F9', 'F10'];
            if (e.code === 'Space') {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
                const now = Date.now();
                if (now - lastSpacePress < 250) {
                    e.preventDefault();
                    toggleFullScreen();
                }
                lastSpacePress = now;
            }
            if (targetedKeys.includes(e.key)) {
                e.preventDefault();
            }
            switch (e.key) {
                case 'F1':
                    productSearchInput.value?.focus();
                    break;
                case 'F7':
                    createQuotation();
                    break;
                case 'F8':
                    suspendSale();
                    break;
                case 'F9':
                    openSuspendedSales();
                    break;
                case 'F10':
                    if (formData.value.subtotal > 0 && formData.value.user_id) payNow();
                    break;
                case '?':
                    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                        helpVisible.value = true;
                    }
                    break;
                case 'Escape':
                    helpVisible.value = false;
                    break;
            }
        };

        return {
            isFullScreen, currentTime, currentDate, toggleFullScreen,
            taxes, customers, categories, brands, productLists, productsLoading,
            formData, reFetchProducts, selectSaleProduct,
            taxChanged, quantityChanged, recalculateFinalTotal,
            payNow, payNowVisible, payNowClosed, resetPos,
            appSetting, permsArray, ...toRefs(state),
            fetchProducts, onProductSearch, searchValueSelected,
            selectedProducts, orderItemColumns,
            formatAmount, formatAmountCurrency,
            customerAdded,
            editItem, addEditVisible, addEditFormData, addEditFormSubmitting,
            addEditRules, addEditPageTitle, onAddEditSubmit, onAddEditClose,
            taxTypes, showDeleteConfirm,
            payNowSuccess, printInvoiceModalVisible, printInvoiceOrder,
            postLayout, inputValueChanged,
            suspendedSales, showSuspendModal, suspendSale, openSuspendedSales,
            restoreSale, suspendColumns, deleteSuspendedSale,
            helpVisible, closeHelpModal, productSearchInput,
            createQuotation,
        };
    },
};
</script>

<style lang="less" scoped>
.pos-page {
    display: flex;
    flex-direction: column;
    height: 100vh;
    background: #f5f6fa;
    overflow: hidden; // desktop: no page scroll, panels scroll internally
}

// Form wrapper must stretch to fill remaining height
.pos-form-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0; // allow shrinking within flex parent
}

// Header
.pos-header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background: #fff;
    border-bottom: 1px solid #e8e8e8;
    flex-shrink: 0;
    z-index: 10;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.back-btn {
    font-size: 16px;
    color: #8c8c8c;
    &:hover { color: #1890ff; background: #e6f7ff; }
}

.header-titles {
    display: flex;
    flex-direction: column;
    .pos-title {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.2;
        color: #1a1a1a;
    }
}

.modern-breadcrumb {
    font-size: 11px;
    color: #bfbfbf;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    :deep(.ant-breadcrumb-link) { color: #bfbfbf; }
    .active { color: #8c8c8c; font-weight: 600; }
}

.pos-clock {
    text-align: center;
    .time {
        font-family: 'SF Mono', 'Fira Code', monospace;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        display: block;
        letter-spacing: 1px;
    }
    .date {
        font-size: 11px;
        color: #94a3b8;
    }
}

.action-icon-btn {
    border: none;
    background: #f5f5f5;
    color: #595959;
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.2s;
    &:hover { background: #e6f7ff; color: #1890ff; }
}

// Body layout
.pos-body {
    display: flex;
    flex: 1;
    min-height: 0; // critical: allow flex child to shrink and scroll
    overflow: hidden;
}

.pos-left-panel {
    width: 42%;
    min-width: 380px;
    display: flex;
    flex-direction: column;
    background: #fff;
    border-right: 1px solid #e8e8e8;
    height: 100%; // fill full body height on desktop
    min-height: 0; // allow flex children to shrink/scroll
}

.pos-right-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    padding: 12px 16px;
}

// Filters card
.pos-filters-card {
    flex-shrink: 0;
    border-bottom: 1px solid #f0f0f0;
    border-radius: 0;
    :deep(.ant-card-body) {
        padding: 12px 16px;
    }
}

.filter-row {
    display: flex;
    gap: 8px;
    align-items: center;
}

.mt-8 { margin-top: 8px; }

// Cart table
.pos-cart-table {
    flex: 1;
    overflow-y: auto;
    min-height: 0; // critical for flex child scrolling
    padding: 0 4px;
    :deep(.ant-table-thead > tr > th) {
        background: #fafafa;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #8c8c8c;
        padding: 8px 12px;
    }

    :deep(.ant-table-tbody > tr > td) {
        padding: 8px 12px;
        font-size: 13px;
    }

    :deep(.ant-table-tbody > tr:hover > td) {
        background: #f9fafb;
    }
}

.cart-product-name {
    font-weight: 500;
    font-size: 13px;
    color: #1a1a1a;
    line-height: 1.3;
}

.cart-stock-info {
    font-size: 11px;
}

// Footer bar — always pinned to bottom of left panel
.pos-footer-bar {
    flex-shrink: 0;
    margin-top: auto; // push to bottom of flex container
    padding: 12px 16px;
    background: #fff;
    border-top: 1px solid #f0f0f0;
    box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.04);
}

.footer-controls {
    margin-bottom: 8px;
}

.compact-form-item {
    margin-bottom: 4px;
    :deep(.ant-form-item-label) {
        padding-bottom: 2px;
        > label {
            font-size: 11px;
            color: #8c8c8c;
            height: auto;
        }
    }
}

.footer-top-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    padding-top: 8px;
    border-top: 1px dashed #e8e8e8;

    .left-meta {
        color: #8c8c8c;
        font-size: 12px;
        .meta-item strong {
            color: #595959;
            margin-right: 4px;
        }
    }
}

.footer-main-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    border-top: 1px solid #e8e8e8;
}

.grand-total-section {
    display: flex;
    align-items: baseline;
    gap: 10px;
    .total-label {
        font-size: 12px;
        color: #8c8c8c;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .total-amount {
        font-size: 28px;
        font-weight: 800;
        color: #1a1a1a;
        line-height: 1;
    }
}

.pay-now-btn {
    height: 50px;
    padding: 0 28px;
    font-size: 16px;
    font-weight: 700;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(24, 144, 255, 0.3);
    display: flex;
    align-items: center;
    gap: 8px;
    &:disabled {
        box-shadow: none;
        background: #f5f5f5;
        color: #bfbfbf;
    }

    .btn-content {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        line-height: 1.2;
    }
    .btn-text { font-size: 16px; font-weight: 700; }
    .btn-shortcut { font-size: 10px; opacity: 0.7; font-weight: 400; }
}

// Action buttons
.btn-quotation {
    border-color: #d9d9d9;
    color: #595959;
    &:hover { border-color: #1890ff; color: #1890ff; background: #e6f7ff; }
}

.btn-suspend {
    background: #fffbe6;
    border-color: #ffe58f;
    color: #d48806;
    &:hover { background: #fff1b8; border-color: #d48806; }
}

.btn-restore {
    background: #f6ffed;
    border-color: #b7eb8f;
    color: #389e0d;
    &:hover { background: #d9f7be; border-color: #389e0d; }
}

.shortcut-badge {
    font-size: 9px;
    background: rgba(0, 0, 0, 0.06);
    padding: 1px 4px;
    border-radius: 3px;
    margin-left: 6px;
    color: #8c8c8c;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

// Product grid
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 12px;
    padding: 8px 0;
}

.product-grid-item {
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    border-radius: 10px;
    &:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    &:active {
        transform: translateY(0);
    }
}

.product-empty-state {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
    min-height: 300px;
}

// Skeleton loading
.product-skeleton-card {
    background: #fff;
    border: 1px solid #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
    .skeleton-img {
        width: 100%;
        height: 120px;
        :deep(.ant-skeleton-image) {
            width: 100%;
            height: 120px;
        }
    }
    .skeleton-body {
        padding: 10px 12px;
    }
}

// Responsive — tablet & mobile
@media (max-width: 992px) {
    .pos-page {
        overflow: visible; // allow page to scroll on mobile
        height: auto;
        min-height: 100vh;
    }

    .pos-body {
        flex-direction: column;
        overflow: visible;
        flex: none;
    }

    .pos-right-panel {
        flex: none;
        max-height: 35vh;
        overflow-y: auto;
        border-bottom: 1px solid #e8e8e8;
        order: -1; // products on top
    }

    .pos-left-panel {
        width: 100%;
        min-width: unset;
        height: auto;
        border-right: none;
        flex: none;

        .pos-cart-table {
            flex: none;
            max-height: 28vh;
            overflow-y: auto;
        }
    }

    // Footer sticks to viewport bottom on mobile
    .pos-footer-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 100;
        padding: 10px 12px;
        background: #fff;
        border-top: 1px solid #e0e0e0;
        box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.08);
    }

    // Hide verbose footer sections on mobile, show compact version
    .footer-controls {
        display: none; // hide tax/discount/shipping on small screens
    }
    .footer-top-actions {
        .left-meta { display: none; } // hide tax/discount summary
    }

    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 8px;
    }
    .shortcut-badge, .btn-shortcut { display: none; }
    .header-center { display: none; }
    .exit-label { display: none; }

    // Add bottom padding to cart so last items aren't hidden behind fixed footer
    .pos-left-panel {
        padding-bottom: 160px;
    }
}

// Responsive — small phones
@media (max-width: 576px) {
    .pos-header-container { padding: 8px 12px; }
    .modern-breadcrumb { display: none; }

    .pos-right-panel {
        max-height: 30vh;
        padding: 8px 10px;
    }

    .footer-top-actions {
        flex-direction: column;
        gap: 6px;
        align-items: stretch;
    }
    .footer-main-row {
        flex-direction: column;
        gap: 8px;
        align-items: stretch;
    }
    .grand-total-section {
        justify-content: space-between;
        .total-amount { font-size: 22px; }
    }
    .pay-now-btn {
        width: 100%;
        justify-content: center;
        height: 44px;
        padding: 0 16px;
    }
    .product-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
}
</style>
