<template>
    <a-modal title="M-Pesa Payment" :open="visible" :footer="null" @cancel="handleClose" @keydown.enter="handleEnterKey"
        @keydown.esc="handleEscKey">
        <a-form v-if="state === 'input'" layout="vertical">
            <a-form-item label="Phone Number" extra="Enter number in format 2547XXXXXXXX">
                <a-input @pressEnter="send" size="medium" v-model:value="phone" placeholder="2547XXXXXXXX" :disabled="processing" ref="phoneInput" />
            </a-form-item>

            <a-alert type="info" :message="`Total Amount: KES ${amount}`" show-icon />

            <div style="margin-top: 16px; text-align: right">
                <a-button @click="handleClose">Cancel</a-button>
                <a-button :loading="processing" type="primary" style="margin-left: 8px" @click="send">
                    {{ processing ? 'Requesting...' : 'Pay Now' }}
                </a-button>
            </div>
        </a-form>

        <div v-else-if="state === 'polling'" style="text-align: center; padding: 24px">
            <a-spin size="large" />
            <h3 style="margin-top: 16px">Waiting for PIN entry...</h3>
            <p>Please check your phone and enter your M-Pesa PIN.</p>
            <a-button danger ghost @click="cancelPolling" style="margin-top: 8px">
                Stop Waiting (Esc)
            </a-button>
        </div>

        <div v-else-if="state === 'result'" style="text-align: center; padding: 24px">
            <a-result :status="paymentSuccess ? 'success' : 'error'"
                :title="paymentSuccess ? 'Payment Successful!' : 'Payment Failed'" :sub-title="resultMessage">
                <template #extra>
                    <a-button size="large" type="primary" @click="handleClose">Close</a-button>
                    <a-button size="large" v-if="!paymentSuccess" @click="resetForm">Try Again</a-button>
                </template>
            </a-result>
        </div>
    </a-modal>
</template>

<script setup>
import { ref, watch, nextTick } from "vue";

const props = defineProps({
    visible: Boolean,
    amount: Number,
});

const emit = defineEmits(["send", "close", "cancelPolling"]);

const state = ref("input");
const phone = ref("");
const processing = ref(false);
const paymentSuccess = ref(false);
const resultMessage = ref("");
const phoneInput = ref(null);


const handleEnterKey = () => {
    if (state.value === 'input' && !processing.value) send();
};

const handleEscKey = () => {
    if (state.value === 'polling') cancelPolling();
};

const send = () => {
    if (!phone.value) return;
    processing.value = true;
    emit("send", { phone: phone.value, amount: props.amount });
};

const startPolling = () => {
    processing.value = false;
    state.value = "polling";
};


const stopPolling = (success, message = "") => {
    paymentSuccess.value = success;
    resultMessage.value = message || (success ? "Transaction completed." : "Transaction was not completed.");
    state.value = "result";
    processing.value = false;
};

const resetForm = () => {
    state.value = "input";
    processing.value = false;
};

const cancelPolling = () => {
    emit("cancelPolling");
    resetForm();
};

const handleClose = () => {
    if (state.value === 'polling') cancelPolling();
    emit("close");
    setTimeout(resetForm, 300);
};


watch(() => props.visible, (isOpen) => {
    if (isOpen) {
        nextTick(() => phoneInput.value?.focus());
    }
});

defineExpose({ startPolling, stopPolling });
</script>
