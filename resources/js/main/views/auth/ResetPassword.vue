<template>
    <div class="login-main-container">
        <a-row class="main-container-div">
            <a-col :xs="24" :sm="24" :md="24" :lg="8">
                <a-row class="login-left-div">
                    <a-col :xs="{ span: 20, offset: 2 }" :sm="{ span: 20, offset: 2 }" :md="{ span: 16, offset: 4 }"
                        :lg="{ span: 16, offset: 4 }">
                        <a-card :bordered="innerWidth <= 768">
                            <a-form layout="vertical">
                                <div class="login-logo mb-30">
                                    <img class="login-img-logo" :src="globalSetting.light_logo_url" />
                                </div>

                                <div class="mb-20 text-center">
                                    <h3>{{ $t('user.reset_password') }}</h3>
                                    <p class="text-muted">Enter your new password below.</p>
                                </div>

                                <a-alert v-if="onRequestSend.error" :message="onRequestSend.error" type="error"
                                    show-icon class="mb-20" />
                                <a-alert v-if="onRequestSend.success" :message="onRequestSend.success" type="success"
                                    show-icon class="mb-20" />

                                <a-form-item :label="$t('user.password')" name="password"
                                    v-bind="validateInfos.password">
                                    <a-input-password v-model:value="resetData.password"
                                        :placeholder="$t('user.password')" />
                                </a-form-item>

                                <a-form-item :label="$t('user.confirm_password')" name="password_confirmation">
                                    <a-input-password v-model:value="resetData.password_confirmation"
                                        :placeholder="$t('user.confirm_password')" />
                                </a-form-item>

                                <a-form-item class="mt-30">
                                    <a-button :loading="loading" @click="onResetSubmit" class="login-btn" block
                                        type="primary">
                                        {{ $t("user.update_password") }}
                                    </a-button>
                                </a-form-item>

                                <div class="login-footer-text">
                                    <router-link :to="{ name: 'admin.login' }">
                                        {{ $t('user.back_to_login') }}
                                    </router-link>
                                </div>
                            </a-form>
                        </a-card>
                    </a-col>
                </a-row>
            </a-col>
            <a-col :xs="0" :sm="0" :md="24" :lg="16">
                <div class="right-login-div">
                    <img class="right-image" :src="loginBackground" />
                </div>
            </a-col>
        </a-row>
    </div>
</template>

<script>
import { defineComponent, reactive, ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";

export default defineComponent({
    setup() {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { globalSetting } = common();
        const route = useRoute();
        const router = useRouter();

        const loginBackground = globalSetting.value.login_image_url;

        const resetData = reactive({
            token: "",
            email: "",
            password: "",
            password_confirmation: "",
        });

        const onRequestSend = ref({ error: "", success: "" });

        onMounted(() => {
            resetData.token = route.query.token;
            resetData.email = route.query.email;

            if (!resetData.token || !resetData.email) {
                onRequestSend.value.error = "The reset link appears to be invalid. Please request a new one.";
            }
        });

        const onResetSubmit = () => {
            onRequestSend.value = { error: "", success: "" };

            addEditRequestAdmin({
                url: "auth/reset-password",
                data: resetData,
                success: (response) => {
                    onRequestSend.value.success = "Password reset successfully! Redirecting...";
                    setTimeout(() => {
                        router.push({ name: "admin.login" });
                    }, 3000);
                },
                error: (err) => {
                    onRequestSend.value = {
                        error: err?.error?.message ? err?.error?.message : "Something went wrong",
                        success: false,
                    };
                },
            });
        };

        return {
            loading,
            rules,
            resetData,
            onResetSubmit,
            onRequestSend,
            globalSetting,
            loginBackground,
            innerWidth: window.innerWidth,
            validateInfos: {}, // Replace with your actual validation logic if using Form.useForm
        };
    },
});
</script>
