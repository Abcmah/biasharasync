<template>
    <a-drawer
        :open="visible"
        :width="drawerWidth"
        :maskClosable="false"
        @close="drawerClosed"
    >
        <a-row :gutter="24">
            <!-- LEFT SUMMARY -->
            <OrderSummary
                :subtotal="data.subtotal"
                :selectedProducts="selectedProducts"
                :totalEnteredAmount="totalEnteredAmount"
            />

            <!-- RIGHT PAYMENTS -->
            <a-col :md="16">
                <PaymentMethods
                    @select="selectMode"
                    @mpesa="openMpesa"
                />

                <CashCardForm
                    v-if="selectedMode === 'cash'"
                    @add="addPayment"
                />

                <PaymentsTable
                    :payments="payments"
                    @remove="removePayment"
                />

                <CompleteOrderBar
                    :disabled="totalEnteredAmount < data.subtotal"
                    @complete="completeOrder"
                />
            </a-col>
        </a-row>

        <!-- MPESA MODAL -->
        <MpesaModal
            :visible="mpesaVisible"
            :amount="data.subtotal"
            @send="sendMpesa"
            @close="mpesaVisible = false"
        />
    </a-drawer>
</template>

<script>
import { ref, computed } from "vue";
import common from "../../../../common/composable/common";
import apiAdmin from "../../../../common/composable/apiAdmin";

/* Components */
import OrderSummary from "./components/OrderSummary.vue";
import PaymentMethods from "./components/PaymentMethods.vue";
import CashCardForm from "./components/CashCardForm.vue";
import PaymentsTable from "./components/PaymentsTable.vue";
import CompleteOrderBar from "./components/CompleteOrderBar.vue";
import MpesaModal from "./components/MpesaModal.vue";

export default {
    props: {
        visible: Boolean,
        data: Object,
        selectedProducts: Array,
    },
    emits: ["closed", "success"],
    components: {
        OrderSummary,
        PaymentMethods,
        CashCardForm,
        PaymentsTable,
        CompleteOrderBar,
        MpesaModal,
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin } = apiAdmin();
        const { formatAmountCurrency } = common();

        /* =========================
         * STATE
         * ========================= */
        const payments = ref([]);
        const selectedMode = ref(null);
        const mpesaVisible = ref(false);

        /* =========================
         * COMPUTED
         * ========================= */
        const totalEnteredAmount = computed(() =>
            payments.value.reduce(
                (sum, p) => sum + Number(p.amount || 0),
                0
            )
        );

        const drawerWidth =
            window.innerWidth <= 991 ? "90%" : "60%";

        /* =========================
         * METHODS
         * ========================= */

        const drawerClosed = () => {
            reset();
            emit("closed");
        };

        const reset = () => {
            payments.value = [];
            selectedMode.value = null;
            mpesaVisible.value = false;
        };

        const selectMode = (mode) => {
            selectedMode.value = mode;
        };

        const addPayment = ({ amount, notes }) => {
            if (!amount || amount <= 0) return;

            payments.value.push({
                id: crypto.randomUUID(),
                method: "cash",
                amount: Number(amount),
                notes,
            });
        };

        const removePayment = (id) => {
            payments.value = payments.value.filter(
                (p) => p.id !== id
            );
        };

        const openMpesa = () => {
            mpesaVisible.value = true;
        };

        const sendMpesa = (phone) => {
            payments.value.push({
                id: crypto.randomUUID(),
                method: "mpesa",
                amount: Number(props.data.subtotal),
                phone,
                status: "pending",
            });

            mpesaVisible.value = false;
        };

        const completeOrder = () => {
            if (totalEnteredAmount.value < props.data.subtotal) {
                return;
            }

            addEditRequestAdmin({
                url: "pos/save",
                data: {
                    payments: payments.value,
                    product_items: props.selectedProducts,
                    details: props.data,
                },
                success: (res) => {
                    reset();
                    emit("success", res.order);
                },
            });
        };

        return {
            formatAmountCurrency,

            payments,
            selectedMode,
            mpesaVisible,

            totalEnteredAmount,
            drawerWidth,

            selectMode,
            addPayment,
            removePayment,
            openMpesa,
            sendMpesa,
            completeOrder,
            drawerClosed,
        };
    },
};
</script>
