<template>
    <div class="checkout-wrapper mt-30 mb-30">
        <a-row type="flex" justify="center">
            <a-col :xs="22" :sm="22" :md="22" :lg="20">
                <a-breadcrumb class="mb-30">
                    <a-breadcrumb-item><router-link to="/">{{ $t("front.home") }}</router-link></a-breadcrumb-item>
                    <a-breadcrumb-item>{{ $t("front.checkout_page") }}</a-breadcrumb-item>
                </a-breadcrumb>

                <a-row :gutter="[30, 30]">
                    <a-col :xs="24" :lg="16">
                        <a-card :bordered="false" class="checkout-card main-form">
                            <section class="checkout-section">
                                <div class="section-title">
                                    <span class="step-number">1</span>
                                    <h3>{{ $t("front.address_details") }}</h3>
                                </div>
                                <a-alert v-if="!selectedAddress" :message="$t('front.select_shipping_address')" type="warning" show-icon class="mb-20" />
                                <UserAddress @onAddressSelection="addressSelected" />
                            </section>

                            <a-divider />

                            <section class="checkout-section">
                                <div class="section-title">
                                    <span class="step-number">2</span>
                                    <h3>{{ $t("front.payment_details") }}</h3>
                                </div>

                                <a-row :gutter="[16, 16]" class="mt-20">
                                    <a-col :xs="24" :md="12">
                                        <div :class="['payment-option', paymentMethod === 'cod' ? 'active' : '']" @click="paymentMethod = 'cod'">
                                            <div class="option-content">
                                                <wallet-outlined />
                                                <span>{{ $t("front.cash_on_delivery") }}</span>
                                            </div>
                                            <a-radio :checked="paymentMethod === 'cod'" />
                                        </div>
                                    </a-col>

                                    <a-col :xs="24" :md="12">
                                        <div :class="['payment-option mpesa', paymentMethod === 'mpesa' ? 'active' : '']" @click="openMpesaModal">
                                            <div class="option-content">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/M-PESA_LOGO-01.svg/1200px-M-PESA_LOGO-01.svg.png" class="mpesa-logo" />
                                                <span>Pay with M-Pesa</span>
                                            </div>
                                            <a-radio :checked="paymentMethod === 'mpesa'" />
                                        </div>
                                    </a-col>
                                </a-row>
                            </section>

                            <div class="action-footer mt-40">
                                <a-button type="ghost" size="large" @click="$router.go(-1)">
                                    <rollback-outlined /> Back
                                </a-button>
                                <a-button
                                    type="primary"
                                    size="large"
                                    :loading="loading"
                                    :disabled="!selectedAddress || products.length === 0"
                                    @click="confirmOrder"
                                >
                                    {{ $t("front.confirm_order") }} <right-outlined />
                                </a-button>
                            </div>
                        </a-card>
                    </a-col>

                    <a-col :xs="24" :lg="8">
                        <a-card :bordered="false" class="checkout-card summary-card">
                            <h3 class="summary-title">Order Summary</h3>
                            <a-list item-layout="horizontal" :data-source="products" class="cart-preview-list">
                                <template #renderItem="{ item }">
                                    <a-list-item>
                                        <a-list-item-meta>
                                            <template #avatar><a-avatar :src="item.image_url" shape="square" :size="50" /></template>
                                            <template #title>{{ item.name }} x {{ item.cart_quantity }}</template>
                                            <template #description>
                                                {{ formatAmountCurrency(getSalesPriceWithTax(item) * item.cart_quantity) }}
                                            </template>
                                        </a-list-item-meta>
                                    </a-list-item>
                                </template>
                            </a-list>
                            <a-divider />
                            <div class="totals-area">
                                <div class="total-row grand-total">
                                    <span>{{ $t("stock.grand_total") }}</span>
                                    <span>{{ formatAmountCurrency(total) }}</span>
                                </div>
                            </div>
                        </a-card>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>

        <a-modal v-model:open="mpesaModalVisible" title="M-Pesa Express Payment" centered @ok="triggerStkPush">
            <div class="mpesa-modal-body">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/M-PESA_LOGO-01.svg/1200px-M-PESA_LOGO-01.svg.png" class="mpesa-logo-large" />
                <p>Enter your M-Pesa phone number to receive a payment prompt.</p>
                <a-form layout="vertical">
                    <a-form-item label="Phone Number (254...)">
                        <a-input v-model:value="mpesaPhone" placeholder="2547XXXXXXXX" size="large">
                            <template #prefix><mobile-outlined /></template>
                        </a-input>
                    </a-form-item>
                </a-form>
                <div class="amount-badge">Total to Pay: {{ formatAmountCurrency(total) }}</div>
            </div>
            <template #footer>
                <a-button key="back" @click="mpesaModalVisible = false">Cancel</a-button>
                <a-button key="submit" type="primary" :loading="stkLoading" @click="triggerStkPush" style="background: #49aa47; border-color: #49aa47">
                    Send STK Push
                </a-button>
            </template>
        </a-modal>
    </div>
</template>

<script>
import { defineComponent, ref, createVNode } from "vue";
import {
    WalletOutlined, RollbackOutlined, RightOutlined,
    DeleteOutlined, ExclamationCircleOutlined, MobileOutlined
} from "@ant-design/icons-vue";
import { Modal, message } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import { useStore } from "vuex";
import cart from "../../../../common/composable/cart";
import UserAddress from "./address/Index.vue";
import apiFront from "../../../../common/composable/apiFront";
import { getSalesPriceWithTax } from "../../../../common/scripts/functions";

export default defineComponent({
    components: { WalletOutlined, RollbackOutlined, RightOutlined, DeleteOutlined, UserAddress, MobileOutlined },
    setup() {
        const { products, total, formatAmountCurrency, frontWarehouse } = cart();
        const { loading, addEditRequestFront } = apiFront();
        const { t } = useI18n();
        const router = useRouter();
        const store = useStore();

        const selectedAddress = ref(null);
        const paymentMethod = ref('cod');
        const mpesaModalVisible = ref(false);
        const mpesaPhone = ref('254');
        const stkLoading = ref(false);

        const openMpesaModal = () => {
            if(!selectedAddress.value) return message.error(t('front.select_shipping_address'));
            paymentMethod.value = 'mpesa';
            mpesaModalVisible.value = true;
        };

        const triggerStkPush = () => {
            stkLoading.value = true;
            // Add your API call to Daraja/Mpesa here
            setTimeout(() => {
                stkLoading.value = false;
                mpesaModalVisible.value = false;
                message.success("STK Push sent! Please check your phone.");
            }, 2000);
        };

        const confirmOrder = () => {
            Modal.confirm({
                title: t("front.confirm_order"),
                icon: createVNode(ExclamationCircleOutlined),
                content: `You are about to place an order for ${formatAmountCurrency(total.value)}. Continue?`,
                onOk() {
                    addEditRequestFront({
                        url: `front/self/checkout-orders/${frontWarehouse.value.slug}`,
                        data: {
                            products: products.value,
                            address_id: selectedAddress.value,
                            payment_method: paymentMethod.value,
                            mpesa_phone: paymentMethod.value === 'mpesa' ? mpesaPhone.value : null
                        },
                        success: (res) => {
                            store.commit("front/addCartItems", []);
                            router.push({ name: "front.checkout.success", params: { uniqueId: res.unique_id, warehouse: frontWarehouse.value.slug } });
                        },
                    });
                },
            });
        };

        return {
            products, total, formatAmountCurrency, frontWarehouse, loading,
            selectedAddress, paymentMethod, mpesaModalVisible, mpesaPhone, stkLoading,
            addressSelected: (id) => selectedAddress.value = id,
            openMpesaModal, triggerStkPush, confirmOrder, getSalesPriceWithTax
        };
    },
});
</script>

<style lang="less" scoped>
.checkout-card {
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    padding: 10px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    .step-number {
        background: #2874f0;
        color: #fff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    h3 { margin: 0; font-weight: 700; }
}

.payment-option {
    border: 2px solid #f1f5f9;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: 0.3s;

    .option-content {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        .mpesa-logo { height: 20px; }
    }

    &:hover { border-color: #cbd5e1; }
    &.active { border-color: #2874f0; background: #eff6ff; }
    &.mpesa.active { border-color: #49aa47; background: #f0fdf4; }
}

.action-footer {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    .ant-btn { border-radius: 8px; height: 48px; padding: 0 30px; }
}

.summary-title { font-weight: 700; margin-bottom: 20px; }
.grand-total {
    display: flex;
    justify-content: space-between;
    font-size: 18px;
    font-weight: 800;
    color: #1e293b;
}

.mpesa-modal-body {
    text-align: center;
    .mpesa-logo-large { height: 40px; margin-bottom: 20px; }
    .amount-badge {
        margin-top: 15px;
        padding: 10px;
        background: #f8fafc;
        border-radius: 8px;
        font-weight: 700;
        font-size: 16px;
    }
}
</style>
