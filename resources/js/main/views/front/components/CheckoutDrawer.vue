<template>
   <a-button type="link" @click="showDrawer" class="cart-trigger-btn">
        <a-badge :count="totalCartItems" :offset="[5, 5]" color="#2874f0">
            <shopping-cart-outlined class="cart-icon" />
        </a-badge>
    </a-button>

    <a-drawer
        v-model:open="visible"
        :width="innerWidth <= 768 ? '100%' : 450"
        placement="right"
        :closable="false"
        class="modern-cart-drawer"
    >
        <template #title>
            <div class="drawer-header-content">
                <shopping-outlined class="header-icon" />
                <span>Your Cart <small>({{ totalCartItems }} items)</small></span>
            </div>
        </template>

        <template #extra>
            <div class="close-btn" @click="closeDrawer">
                <close-outlined />
            </div>
        </template>

        <div v-if="products.length > 0" class="cart-items-wrapper">
            <div v-for="item in products" :key="item.xid" class="cart-item-card">
                <div class="item-image" @click="openPreview(item)">
                    <img :src="item.image_url" alt="product" />
                    <div class="image-overlay"><eye-outlined /></div>
                </div>

                <div class="item-details">
                    <div class="item-header">
                        <h4>{{ item.name }}</h4>
                        <delete-outlined class="delete-icon" @click="removeItem(item.xid)" />
                    </div>
                    <p class="unit-price">{{ formatAmountCurrency(getSalesPriceWithTax(item)) }}</p>

                    <div class="item-footer">
                        <div class="quantity-stepper">
                            <button @click="handleDecrement(item)">-</button>
                            <input type="text" :value="item.cart_quantity" readonly />
                            <button @click="handleIncrement(item)">+</button>
                        </div>
                        <span class="item-total">
                            {{ formatAmountCurrency(getSalesPriceWithTax(item) * item.cart_quantity) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <a-empty v-else description="Your cart is empty" class="empty-cart" />

        <template #footer>
            <div class="cart-footer">
                <div class="subtotal-row">
                    <span class="label">Subtotal</span>
                    <span class="value">{{ formatAmountCurrency(total) }}</span>
                </div>
                <a-button type="primary" class="checkout-btn" @click="proceedCheckout" block size="large">
                    Checkout Now
                    <right-outlined />
                </a-button>
            </div>
        </template>
    </a-drawer>

    <a-modal
        v-model:open="previewVisible"
        :footer="null"
        centered
        :closable="true"
        width="400px"
        class="preview-modal"
    >
        <div class="preview-content">
            <img :src="previewItem?.image_url" class="full-preview-img" />
            <div class="preview-info">
                <h3>{{ previewItem?.name }}</h3>
                <p class="preview-price">{{ formatAmountCurrency(getSalesPriceWithTax(previewItem || {})) }}</p>
            </div>
        </div>
    </a-modal>
</template>
<script>

import { defineComponent, computed, ref, onMounted, watch } from "vue";
import {
    ShoppingCartOutlined,
    ShoppingOutlined,
    CloseOutlined,
    DeleteOutlined,
    RightOutlined,
    EyeOutlined
} from "@ant-design/icons-vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import cart from "../../../..//common/composable/cart";
import { getSalesPriceWithTax } from "../../../../common/scripts/functions";

export default defineComponent({
    emits: ["openLoginModal"],
    components: {
        ShoppingCartOutlined,
        ShoppingOutlined,
        CloseOutlined,
        DeleteOutlined,
        RightOutlined,
        EyeOutlined
    },
    setup(props, { emit }) {
        const store = useStore();
        const visible = ref(false);
        const router = useRouter();
        const {
            products,
            updateCart,
            removeItem,
            total,
            formatAmountCurrency,
            frontWarehouse,
        } = cart();

        const showDrawer = () => (visible.value = true);

        const closeDrawer = () => (visible.value = false);

        const isLoggedIn = computed(() => {
            return store.getters["front/isLoggedIn"];
        });

        const proceedCheckout = () => {
            visible.value = false;

            if (isLoggedIn.value) {
                router.push({
                    name: "front.checkout",
                    params: { warehouse: frontWarehouse.value.slug },
                });
            } else {
                emit("openLoginModal");
            }
        };

        // new
        const previewVisible = ref(false);
        const previewItem = ref(null);

        const openPreview = (item) => {
            previewItem.value = item;
            previewVisible.value = true;
        };

        const handleIncrement = (item) => {
            item.cart_quantity++;
            updateCart();
        };

        const handleDecrement = (item) => {
            if (item.cart_quantity > 1) {
                item.cart_quantity--;
                updateCart();
            } else {
                removeItem(item.xid);
            }
        };

        return {
            visible,
            showDrawer,
            closeDrawer,
            totalCartItems: computed(() => store.getters["front/totalCartItems"]),

            products,
            removeItem,
            updateCart,
            formatAmountCurrency,
            total,
            proceedCheckout,
            getSalesPriceWithTax,
            frontWarehouse,
            previewVisible,
            previewItem,
            openPreview,
            handleIncrement,
            handleDecrement,
            innerWidth: window.innerWidth,
        };
    },
});
</script>
<style lang="less" scoped>
.cart-trigger-btn {
    padding: 0;
    height: auto;
    .cart-icon {
        font-size: 26px;
        color: #1f2937; // Darker for the new white header
        vertical-align: middle;
        transition: transform 0.2s;
    }
    &:hover .cart-icon { transform: translateY(-2px); }
}

/* Drawer Styling */
.modern-cart-drawer {
    :deep(.ant-drawer-header) {
        border-bottom: 1px solid #f3f4f6;
        padding: 20px 24px;
    }

    .drawer-header-content {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        .header-icon { color: #2874f0; }
        small { font-weight: 400; color: #9ca3af; margin-left: 5px; }
    }

    .close-btn {
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: background 0.2s;
        &:hover { background: #f3f4f6; }
    }
}

/* Cart Item Cards */
.cart-items-wrapper {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.cart-item-card {
    display: flex;
    gap: 15px;
    padding: 12px;
    border-radius: 12px;
    background: #fff;
    border: 1px solid #f3f4f6;
    transition: box-shadow 0.2s;

    &:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); }

    .item-image img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        background: #f9fafb;
    }

    .item-details {
        flex: 1;
        .item-header {
            display: flex;
            justify-content: space-between;
            h4 { margin: 0; font-size: 15px; font-weight: 600; color: #1f2937; }
            .delete-icon { color: #ef4444; cursor: pointer; opacity: 0.6; transition: 0.2s; &:hover { opacity: 1; }}
        }
        .unit-price { font-size: 13px; color: #6b7280; margin: 4px 0 12px; }
    }

    .item-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        .item-total { font-weight: 700; color: #1f2937; }
    }
}

/* Custom Stepper */
.quantity-stepper {
    display: flex;
    align-items: center;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    overflow: hidden;

    button {
        border: none;
        background: #f9fafb;
        padding: 4px 10px;
        cursor: pointer;
        &:hover { background: #f3f4f6; }
    }
    input {
        width: 35px;
        text-align: center;
        border: none;
        border-left: 1px solid #e5e7eb;
        border-right: 1px solid #e5e7eb;
        font-size: 13px;
    }
}

/* Footer Styling */
.cart-footer {
    padding: 10px 0;
    .subtotal-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        .label { color: #6b7280; font-size: 16px; }
        .value { font-size: 20px; font-weight: 800; color: #1f2937; }
    }
    .shipping-note { font-size: 12px; color: #9ca3af; margin-bottom: 20px; }
    .checkout-btn {
        height: 50px;
        border-radius: 12px;
        font-weight: 600;
        background: #2874f0;
        box-shadow: 0 4px 14px rgba(40, 116, 240, 0.3);
    }
}

.empty-cart { padding: 40px 0; }

.item-image {
    position: relative;
    cursor: pointer;
    overflow: hidden;
    border-radius: 8px;

    img {
        width: 80px;
        height: 80px;
        transition: transform 0.3s ease;
    }

    .image-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(40, 116, 240, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    &:hover {
        img { transform: scale(1.1); }
        .image-overlay { opacity: 1; }
    }
}

.preview-modal {
    :deep(.ant-modal-content) {
        border-radius: 20px;
        overflow: hidden;
        padding: 0;
    }

    .full-preview-img {
        width: 100%;
        height: auto;
        display: block;
    }

    .preview-info {
        padding: 20px;
        text-align: center;
        h3 { margin: 0; font-weight: 700; }
        .preview-price { color: #2874f0; font-size: 18px; font-weight: 600; margin-top: 5px; }
    }
}
</style>
