<template>
    <div class="product-card-modern" v-if="currentProduct && currentProduct.xid">
        <div class="product-image-wrapper">
            <div class="sale-badge" v-if="currentProduct.details.mrp > getSalesPriceWithTax(currentProduct)">
                Sale
            </div>
            <a href="javascript:void(0)" @click="showModal">
                <img :src="currentProduct.image_url" class="main-img" />
            </a>
            <div class="quick-view-overlay" @click="showModal">
                <span>Quick View</span>
            </div>
        </div>

        <div class="product-info">
            <div class="info-top">
                <span class="category-tag">{{ currentProduct.category.name }}</span>
                <h3 class="title" @click="showModal">{{ currentProduct.name }}</h3>
            </div>

            <div class="info-bottom">
                <div class="price-stack">
                    <span class="current-price">
                        {{ formatAmountCurrency(getSalesPriceWithTax(currentProduct)) }}
                    </span>
                    <del class="old-price" v-if="currentProduct.details.mrp > getSalesPriceWithTax(currentProduct)">
                        {{ formatAmountCurrency(currentProduct.details.mrp) }}
                    </del>
                </div>

                <div class="cart-action">
                    <transition name="scale" mode="out-in">
                        <div v-if="currentProduct.cart_quantity > 0" class="quantity-toggle">
                            <button @click="updateQty(-1)"><minus-outlined /></button>
                            <span class="qty-count">{{ currentProduct.cart_quantity }}</span>
                            <button @click="updateQty(1)"><plus-outlined /></button>
                        </div>
                        <a-button
                            v-else
                            type="primary"
                            shape="circle"
                            class="add-btn"
                            @click="updateQty(1)"
                        >
                            <template #icon><ShoppingCartOutlined /></template>
                        </a-button>
                    </transition>
                </div>
            </div>
        </div>
    </div>

    <a-modal v-model:open="visible" centered :footer="null" :width="800" class="modern-product-modal">
        <a-row :gutter="[32, 32]">
            <a-col :xs="24" :sm="12">
                <div class="modal-image">
                    <img :src="currentProduct.image_url" />
                </div>
            </a-col>
            <a-col :xs="24" :sm="12">
                <div class="modal-details">
                    <span class="modal-cat">{{ currentProduct.category?.name }}</span>
                    <h2 class="modal-title">{{ currentProduct.name }}</h2>
                    <div class="modal-price">
                        {{ formatAmountCurrency(getSalesPriceWithTax(currentProduct)) }}
                    </div>

                    <p class="modal-desc">{{ currentProduct.description }}</p>

                    <div class="modal-actions">
                         <a-button-group v-if="currentProduct.cart_quantity > 0" size="large">
                            <a-button @click="updateQty(-1)"><minus-outlined /></a-button>
                            <a-button disabled style="color: #000; cursor: default">{{ currentProduct.cart_quantity }}</a-button>
                            <a-button @click="updateQty(1)"><plus-outlined /></a-button>
                        </a-button-group>
                        <a-button v-else type="primary" size="large" block @click="updateQty(1)">
                            <ShoppingCartOutlined /> Add to Cart
                        </a-button>
                    </div>
                </div>
            </a-col>
        </a-row>
    </a-modal>
</template>

<script>
import { ref, onMounted, watch } from "vue";
import { useStore } from "vuex";
import { ShoppingCartOutlined, MinusOutlined, PlusOutlined } from "@ant-design/icons-vue";
import { message } from "ant-design-vue";
import { filter, find } from "lodash-es";
import cart from "../../../../common/composable/cart";
import { getSalesPriceWithTax } from "../../../../common/scripts/functions";

export default {
    props: ["product", "currency"],
    components: { ShoppingCartOutlined, MinusOutlined, PlusOutlined },
    setup(props) {
        const { formatAmountCurrency } = cart();
        const store = useStore();
        const visible = ref(false);
        const currentProduct = ref({ ...props.product, cart_quantity: 0 });

        const syncCart = () => {
            const cartItems = store.getters["front/storeCartItems"];
            const item = find(cartItems, { xid: props.product.xid });
            currentProduct.value.cart_quantity = item ? item.cart_quantity : 0;
        };

        onMounted(syncCart);
        watch(() => store.getters["front/storeCartItems"], syncCart, { deep: true });

        const updateQty = (change) => {
            const newQty = currentProduct.value.cart_quantity + change;
            const cartItems = store.getters["front/storeCartItems"];
            const otherItems = filter(cartItems, i => i.xid !== props.product.xid);

            if (newQty > 0) {
                otherItems.push({ ...props.product, cart_quantity: newQty });
            }

            store.commit("front/addCartItems", otherItems);
            message.success(newQty > 0 ? "Cart updated" : "Removed from cart");
        };

        return {
            currentProduct,
            formatAmountCurrency,
            visible,
            showModal: () => visible.value = true,
            updateQty,
            getSalesPriceWithTax,
        };
    },
};
</script>

<style lang="less" scoped>
.product-card-modern {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #f1f5f9;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;

    &:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);

        .quick-view-overlay { opacity: 1; }
    }
}

.product-image-wrapper {
    position: relative;
    padding: 12px;
    background: #f8fafc;
    height: 200px;
    overflow: hidden;

    .main-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.5s ease;
    }

    .sale-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #ef4444;
        color: #fff;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        z-index: 2;
        text-transform: uppercase;
    }

    .quick-view-overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        cursor: pointer;
        span {
            background: #fff;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
        }
    }
}

.product-info {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;

    .category-tag {
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .title {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin: 4px 0 12px;
        height: 44px;
        overflow: hidden;
        line-height: 1.4;
        cursor: pointer;
        &:hover { color: #2874f0; }
    }
}

.info-bottom {
    margin-top: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.price-stack {
    display: flex;
    flex-direction: column;
    .current-price {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }
    .old-price {
        font-size: 13px;
        color: #94a3b8;
    }
}

/* Custom Quantity Toggle */
.quantity-toggle {
    display: flex;
    align-items: center;
    background: #f1f5f9;
    border-radius: 30px;
    padding: 4px;

    button {
        border: none;
        background: #fff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        &:hover { background: #2874f0; color: #fff; }
    }

    .qty-count {
        margin: 0 10px;
        font-weight: 700;
        font-size: 14px;
        min-width: 20px;
        text-align: center;
    }
}

.add-btn {
    width: 40px !important;
    height: 40px !important;
    background: #2874f0 !important;
    border: none !important;
    &:hover { transform: scale(1.1); }
}

/* Scale Animation */
.scale-enter-active, .scale-leave-active { transition: all 0.2s ease; }
.scale-enter-from, .scale-leave-to { transform: scale(0.8); opacity: 0; }

/* Modern Product Modal Styling */
.modern-product-modal {
    :deep(.ant-modal-content) {
        border-radius: 24px;
        overflow: hidden;
        padding: 0; // We handle padding inside the row
    }

    :deep(.ant-modal-close) {
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(4px);
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        &:hover { transform: rotate(90deg); background: #fff; }
    }

    .modal-image {
        background: #f8fafc;
        height: 100%;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;

        img {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.05));
        }
    }

    .modal-details {
        padding: 40px 40px 40px 0; // Right side padding
        display: flex;
        flex-direction: column;
        height: 100%;

        .modal-cat {
            display: inline-block;
            background: #eff6ff;
            color: #2874f0;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            width: fit-content;
        }

        .modal-title {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .modal-price {
            font-size: 32px;
            font-weight: 900;
            color: #2874f0;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            &::after {
                content: "Incl. Tax";
                font-size: 12px;
                color: #94a3b8;
                margin-left: 10px;
                font-weight: 400;
            }
        }

        .modal-desc {
            font-size: 15px;
            line-height: 1.6;
            color: #64748b;
            margin-bottom: 30px;
            max-height: 150px;
            overflow-y: auto;
            padding-right: 10px;

            /* Custom Scrollbar */
            &::-webkit-scrollbar { width: 4px; }
            &::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        }

        .modal-actions {
            margin-top: auto; // Pushes buttons to the bottom

            :deep(.ant-btn-group) {
                display: flex;
                width: 100%;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 4px 12px rgba(0,0,0,0.08);

                .ant-btn {
                    flex: 1;
                    height: 54px;
                    border: none;
                    background: #f1f5f9;
                    font-size: 18px;
                    &:hover { background: #e2e8f0; }
                    &:last-child, &:first-child { background: #2874f0; color: #fff; }
                }

                // The number display in the middle
                .ant-btn[disabled] {
                    background: #fff !important;
                    font-weight: 800;
                    font-size: 20px;
                    border-left: 1px solid #f1f5f9 !important;
                    border-right: 1px solid #f1f5f9 !important;
                }
            }

            .ant-btn-primary.block {
                height: 54px;
                border-radius: 12px;
                font-size: 16px;
                font-weight: 700;
                background: #2874f0;
                box-shadow: 0 8px 20px rgba(40, 116, 240, 0.3);
            }
        }
    }
}

/* Mobile Adjustments for the Modal */
@media (max-width: 767px) {
    .modern-product-modal {
        .modal-image {
            min-height: 250px;
            padding: 20px;
            img { max-height: 250px; }
        }
        .modal-details {
            padding: 20px 24px 30px 24px;
            .modal-title { font-size: 22px; }
            .modal-price { font-size: 26px; }
        }
    }
}
</style>
