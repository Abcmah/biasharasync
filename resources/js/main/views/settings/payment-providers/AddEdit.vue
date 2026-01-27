<template>
    <a-modal :open="visible" :closable="false" :centered="true" :title="pageTitle" width="800px" @ok="onSubmit">
        <a-form layout="vertical">
            <a-form-item label="Payment Mode" class="required">
                <a-select v-model:value="formData.payment_mode_id">
                    <a-select-option v-for="mode in paymentModes" :key="mode.xid" :value="mode.xid">
                        {{ mode.name }}
                    </a-select-option>
                </a-select>
            </a-form-item>

            <!-- BASIC PROVIDER INFO -->
            <a-row :gutter="16">
                <a-col :span="12">
                    <a-form-item label="Provider Name" class="required">
                        <a-input v-model:value="formData.name" placeholder="e.g Stripe, Mpesa" />
                    </a-form-item>
                </a-col>

                <a-col :span="12">
                    <a-form-item label="Provider Code" class="required">
                        <a-select v-model:value="formData.code" placeholder="Select provider">
                            <a-select-option v-for="(schema, key) in providerSchemas" :key="key" :value="key">
                                {{ key.toUpperCase() }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>

            <!-- MODE / ENV -->
            <a-row :gutter="16">
                <a-col :span="12">
                    <a-form-item label="Environment">
                        <a-radio-group v-model:value="formData.mode_type" button-style="solid">
                            <a-radio-button value="sandbox">Sandbox</a-radio-button>
                            <a-radio-button value="live">Live</a-radio-button>
                        </a-radio-group>
                    </a-form-item>
                </a-col>

                <a-col :span="12">
                    <a-form-item label="Priority">
                        <a-input-number v-model:value="formData.priority" :min="1" style="width: 100%" />
                    </a-form-item>
                </a-col>
            </a-row>

            <a-form-item label="Active">
                <a-switch v-model:checked="formData.is_active" />
            </a-form-item>

            <!-- PROVIDER CONFIG -->
            <a-divider orientation="left">Provider Configuration</a-divider>

            <a-alert v-if="!currentSchema.length" type="info" show-icon
                message="Select a provider to configure credentials" class="mb-3" />

            <a-row :gutter="16" v-if="currentSchema.length">
                <a-col v-for="field in visibleSchema" :key="field.key" :span="12">
                    <a-form-item :label="field.label" :class="{ required: field.required || isRequiredIf(field) }">

                        <!-- TEXT / URL -->
                        <a-input v-if="field.type === 'text' || field.type === 'url'"
                            v-model:value="formData.config[field.key]" />

                        <!-- PASSWORD -->
                        <a-input-password v-else-if="field.type === 'password'"
                            v-model:value="formData.config[field.key]" />

                        <!-- SELECT -->
                        <a-select v-else-if="field.type === 'select'" v-model:value="formData.config[field.key]">
                            <a-select-option v-for="opt in field.options" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </a-select-option>
                        </a-select>

                    </a-form-item>
                </a-col>
            </a-row>


        </a-form>

        <!-- FOOTER -->
        <template #footer>
            <a-button @click="onClose">
                Cancel
            </a-button>

            <a-button type="primary" :loading="loading" @click="onSubmit">
                <SaveOutlined />
                {{ addEditType === 'add' ? 'Create' : 'Update' }}
            </a-button>
        </template>
    </a-modal>
</template>

<script>
import { defineComponent, computed, watch, ref, onMounted } from "vue";
import { SaveOutlined } from "@ant-design/icons-vue";
import apiAdmin from "../../../../common/composable/apiAdmin";
import axios from "axios";
export default defineComponent({
    name: "PaymentProviderModal",
    components: {
        SaveOutlined,
    },
    props: {
        visible: Boolean,
        formData: Object,
        url: String,
        addEditType: String,
        pageTitle: String,
        successMessage: String,
    },

    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();

        /**
         * Provider schemas
         */
        const providerSchemas = {
            mpesa: [
                {
                    key: "transaction_type",
                    label: "MPESA Transaction Type",
                    type: "select",
                    required: true,
                    options: [
                        { label: "Paybill", value: "paybill" },
                        { label: "Till Number", value: "till" },
                    ],
                },
                { key: "consumer_key", label: "Consumer Key", type: "password", required: true },
                { key: "consumer_secret", label: "Consumer Secret", type: "password", required: true },

                // Paybill
                { key: "shortcode", label: "Business Shortcode", type: "text", required: true },
                { key: "passkey", label: "Passkey", type: "password", required: true },

                // Till
                // { key: "till_number", label: "Till Number", type: "text", requiredIf: "till" },

                { key: "callback_url", label: "Callback URL", type: "url", required: true },
            ],


            stripe: [
                { key: "public_key", label: "Public Key", type: "text", required: true },
                { key: "secret_key", label: "Secret Key", type: "password", required: true },
                { key: "webhook_secret", label: "Webhook Secret", type: "password", required: false },
            ],

            paystack: [
                { key: "public_key", label: "Public Key", type: "text", required: true },
                { key: "secret_key", label: "Secret Key", type: "password", required: true },
            ],

            bank: [
                { key: "client_id", label: "Client ID", type: "text", required: true },
                { key: "client_secret", label: "Client Secret", type: "password", required: true },
                { key: "base_url", label: "API Base URL", type: "url", required: true },
                { key: "account_number", label: "Account Number", type: "text", required: true },
            ],
        };

        /**
         * Active provider schema
         */
        const currentSchema = computed(() => {
            return providerSchemas[props.formData.code] || [];
        });

        /**
         * Ensure config object exists & reset on provider change
         */
        watch(
            () => props.formData.code,
            () => {
                props.formData.config = props.formData.config || {};
            }
        );
        const visibleSchema = computed(() => {
            return currentSchema.value.filter(field => {
                if (!field.requiredIf) return true;

                return props.formData.config?.transaction_type === field.requiredIf;
            });
        });

        const isRequiredIf = (field) => {
            return (
                field.requiredIf &&
                props.formData.config?.transaction_type === field.requiredIf
            );
        };
        const paymentModes = ref([]);


        onMounted(async () => {

            paymentModes.value = await fetchPaymentModes();
        });

        const onSubmit = () => {
            addEditRequestAdmin({
                url: props.url,
                data: props.formData,
                successMessage: props.successMessage,
                success: (res) => {
                    emit("addEditSuccess", res.xid);
                },
            });
        };
        const fetchPaymentModes = async () => {
            try {
                const response = await axios.get("/api/v1/payment-modes", {
                    params: {
                        fields: "id,xid,name,mode_type",
                    },
                });

                return response.data.data || response.data;
            } catch (error) {
                console.error("Failed to fetch payment modes", error);
                return [];
            }
        };
        const onClose = () => {
            rules.value = {};
            emit("closed");
        };

        return {
            loading,
            rules,
            providerSchemas,
            currentSchema,
            onSubmit,
            onClose,
            currentSchema,
            paymentModes,
            visibleSchema,
            isRequiredIf
        };
    },
});
</script>

<style scoped>
.required label::after {
    content: " *";
    color: red;
}

.mb-3 {
    margin-bottom: 16px;
}
</style>
