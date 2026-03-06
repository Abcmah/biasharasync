<template>
    <div class="onboarding-container">
        <div class="onboarding-wrapper">
            <div class="onboarding-logo">
                <img :src="globalSetting.light_logo_url" alt="logo" />
            </div>

            <a-steps :current="currentStep" class="onboarding-steps">
                <a-step title="Welcome" />
                <a-step title="Plan" />
                <a-step title="Company" />
                <a-step title="Done" />
            </a-steps>

            <div class="step-content">
                <!-- Step 0: Welcome -->
                <div v-if="currentStep === 0" class="step-welcome">
                    <h1>Welcome, {{ user?.name || 'there' }}!</h1>
                    <p class="step-description">
                        Let's set up your company account. This will only take a few minutes.
                    </p>
                    <div class="user-info-card">
                        <a-avatar :size="64" style="background-color: #1890ff">
                            {{ (user?.name || 'U').charAt(0).toUpperCase() }}
                        </a-avatar>
                        <div class="user-details">
                            <h3>{{ user?.name }}</h3>
                            <p>{{ user?.email }}</p>
                        </div>
                    </div>
                    <a-button type="primary" size="large" block class="step-btn" @click="currentStep = 1">
                        Get Started
                    </a-button>
                </div>

                <!-- Step 1: Plan Selection -->
                <div v-if="currentStep === 1" class="step-plans">
                    <h2>Choose your plan</h2>
                    <p class="step-description">Select the plan that best fits your business needs.</p>

                    <a-spin :spinning="plansLoading">
                        <a-row :gutter="[16, 16]" class="plans-grid">
                            <a-col :xs="24" :sm="12" :lg="8" v-for="plan in plans" :key="plan.xid">
                                <div
                                    class="plan-card"
                                    :class="{ 'plan-selected': selectedPlan === plan.xid, 'plan-popular': plan.is_popular }"
                                    @click="selectedPlan = plan.xid"
                                >
                                    <div v-if="plan.is_popular" class="popular-badge">Popular</div>
                                    <h3>{{ plan.name }}</h3>
                                    <p class="plan-description">{{ plan.description }}</p>
                                    <div class="plan-price">
                                        <span class="price-amount">${{ plan.monthly_price }}</span>
                                        <span class="price-period">/month</span>
                                    </div>
                                    <ul class="plan-features" v-if="plan.features && plan.features.length">
                                        <li v-for="(feature, idx) in plan.features" :key="idx">
                                            <CheckOutlined style="color: #52c41a; margin-right: 8px;" />
                                            {{ feature }}
                                        </li>
                                    </ul>
                                    <div class="plan-products" v-if="plan.max_products">
                                        Up to {{ plan.max_products }} products
                                    </div>
                                </div>
                            </a-col>
                        </a-row>
                    </a-spin>

                    <div class="step-actions">
                        <a-button size="large" @click="currentStep = 0">Back</a-button>
                        <a-button
                            type="primary"
                            size="large"
                            :disabled="!selectedPlan"
                            @click="currentStep = 2"
                        >
                            Continue
                        </a-button>
                    </div>
                </div>

                <!-- Step 2: Company Details -->
                <div v-if="currentStep === 2" class="step-company">
                    <h2>Create your company</h2>
                    <p class="step-description">Tell us about your business.</p>

                    <a-alert v-if="formError" :message="formError" type="error" show-icon class="mb-16" closable @close="formError = ''" />

                    <a-form layout="vertical" class="company-form">
                        <a-form-item
                            label="Company Name"
                            required
                            :help="rules.name ? rules.name.message : null"
                            :validateStatus="rules.name ? 'error' : null"
                        >
                            <a-input
                                size="large"
                                v-model:value="companyForm.name"
                                placeholder="Enter your company name"
                            />
                        </a-form-item>

                        <a-form-item
                            label="Business Email"
                            :help="rules.email ? rules.email.message : null"
                            :validateStatus="rules.email ? 'error' : null"
                        >
                            <a-input
                                size="large"
                                v-model:value="companyForm.email"
                                placeholder="contact@yourcompany.com"
                            />
                        </a-form-item>

                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="12">
                                <a-form-item label="Phone">
                                    <a-input
                                        size="large"
                                        v-model:value="companyForm.phone"
                                        placeholder="+254 700 000 000"
                                    />
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="12">
                                <a-form-item label="Address">
                                    <a-input
                                        size="large"
                                        v-model:value="companyForm.address"
                                        placeholder="Business address"
                                    />
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-form>

                    <div class="step-actions">
                        <a-button size="large" @click="currentStep = 1">Back</a-button>
                        <a-button
                            type="primary"
                            size="large"
                            :loading="loading"
                            :disabled="!companyForm.name"
                            @click="submitCompany"
                        >
                            Create Company
                        </a-button>
                    </div>
                </div>

                <!-- Step 3: Success -->
                <div v-if="currentStep === 3" class="step-success">
                    <a-result
                        status="success"
                        title="Your company is ready!"
                        sub-title="Everything is set up. You can now start managing your business."
                    >
                        <template #extra>
                            <a-button type="primary" size="large" @click="goToDashboard">
                                Go to Dashboard
                            </a-button>
                        </template>
                    </a-result>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent, ref, reactive, onMounted } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import { CheckOutlined } from "@ant-design/icons-vue";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";

export default defineComponent({
    components: { CheckOutlined },
    setup() {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { globalSetting } = common();
        const store = useStore();
        const router = useRouter();

        const user = store.state.auth.user;
        const currentStep = ref(0);
        const selectedPlan = ref(null);
        const plans = ref([]);
        const plansLoading = ref(false);
        const formError = ref("");

        const companyForm = reactive({
            name: "",
            email: user?.email || "",
            phone: "",
            address: "",
        });

        const fetchPlans = () => {
            plansLoading.value = true;
            axiosAdmin
                .get("/onboarding/plans")
                .then((response) => {
                    plans.value = response.data.plans || [];
                    // Auto-select popular or first plan
                    const popular = plans.value.find((p) => p.is_popular);
                    if (popular) {
                        selectedPlan.value = popular.xid;
                    } else if (plans.value.length > 0) {
                        selectedPlan.value = plans.value[0].xid;
                    }
                })
                .catch(() => {
                    formError.value = "Failed to load plans. Please refresh.";
                })
                .finally(() => {
                    plansLoading.value = false;
                });
        };

        const submitCompany = () => {
            formError.value = "";

            addEditRequestAdmin({
                url: "onboarding/company",
                data: {
                    name: companyForm.name,
                    email: companyForm.email || null,
                    phone: companyForm.phone || null,
                    address: companyForm.address || null,
                    subscription_plan_id: selectedPlan.value,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                },
                success: (response) => {
                    store.commit("auth/updateUser", response.user);
                    store.commit("auth/updateApp", response.app);
                    store.commit(
                        "auth/updateVisibleSubscriptionModules",
                        response.visible_subscription_modules
                    );

                    if (response.shortcut_menus) {
                        store.commit(
                            "auth/updateAddMenus",
                            response.shortcut_menus.credentials
                        );
                    }

                    store.dispatch("auth/updateAllWarehouses");
                    store.commit("auth/updateWarehouse", response.user.warehouse);

                    currentStep.value = 3;
                },
                error: (err) => {
                    formError.value =
                        err && err.error && err.error.message
                            ? err.error.message
                            : "Failed to create company. Please try again.";
                },
            });
        };

        const goToDashboard = () => {
            router.push({
                name: "admin.dashboard.index",
                params: { success: true },
            });
        };

        onMounted(() => {
            fetchPlans();
        });

        return {
            globalSetting,
            user,
            currentStep,
            selectedPlan,
            plans,
            plansLoading,
            companyForm,
            formError,
            loading,
            rules,
            submitCompany,
            goToDashboard,
        };
    },
});
</script>

<style lang="less" scoped>
.onboarding-container {
    min-height: 100vh;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.onboarding-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 40px;
    max-width: 800px;
    width: 100%;
    box-shadow: 0 2px 16px rgba(0, 0, 0, 0.08);
}

.onboarding-logo {
    text-align: center;
    margin-bottom: 32px;
    img {
        height: 40px;
        object-fit: contain;
    }
}

.onboarding-steps {
    margin-bottom: 40px;
}

.step-content {
    min-height: 300px;
}

/* Welcome Step */
.step-welcome {
    text-align: center;
    max-width: 450px;
    margin: 0 auto;

    h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 12px;
    }
}

.step-description {
    color: #8c8c8c;
    font-size: 15px;
    margin-bottom: 32px;
}

.user-info-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: #fafafa;
    border-radius: 10px;
    margin-bottom: 32px;
    justify-content: center;

    .user-details {
        text-align: left;
        h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }
        p {
            margin: 0;
            color: #8c8c8c;
            font-size: 14px;
        }
    }
}

.step-btn {
    height: 48px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
}

/* Plans Step */
.step-plans {
    h2 {
        font-size: 22px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 8px;
    }
}

.plans-grid {
    margin-bottom: 32px;
}

.plan-card {
    border: 2px solid #f0f0f0;
    border-radius: 10px;
    padding: 24px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    height: 100%;

    &:hover {
        border-color: #91d5ff;
    }

    &.plan-selected {
        border-color: #1890ff;
        background: #e6f7ff;
    }

    &.plan-popular {
        border-color: #b7eb8f;
    }

    h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .plan-description {
        color: #8c8c8c;
        font-size: 13px;
        margin-bottom: 16px;
    }
}

.popular-badge {
    position: absolute;
    top: -1px;
    right: 16px;
    background: #52c41a;
    color: white;
    padding: 2px 12px;
    border-radius: 0 0 6px 6px;
    font-size: 12px;
    font-weight: 600;
}

.plan-price {
    margin-bottom: 16px;

    .price-amount {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .price-period {
        font-size: 14px;
        color: #8c8c8c;
    }
}

.plan-features {
    list-style: none;
    padding: 0;
    margin: 0 0 12px 0;

    li {
        padding: 4px 0;
        font-size: 13px;
        color: #595959;
    }
}

.plan-products {
    font-size: 12px;
    color: #8c8c8c;
    padding-top: 8px;
    border-top: 1px solid #f0f0f0;
}

/* Company Step */
.step-company {
    max-width: 550px;
    margin: 0 auto;

    h2 {
        font-size: 22px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 8px;
    }
}

.company-form {
    margin-bottom: 24px;
}

/* Actions */
.step-actions {
    display: flex;
    justify-content: space-between;
    gap: 12px;

    .ant-btn {
        min-width: 120px;
        height: 44px;
        border-radius: 8px;
    }
}

.mb-16 {
    margin-bottom: 16px;
}

/* Success Step */
.step-success {
    padding: 40px 0;
}

/* Responsive */
@media (max-width: 576px) {
    .onboarding-wrapper {
        padding: 24px 16px;
    }

    .step-welcome h1 {
        font-size: 22px;
    }

    .plan-price .price-amount {
        font-size: 22px;
    }

    .step-actions {
        flex-direction: column-reverse;
    }
}
</style>
