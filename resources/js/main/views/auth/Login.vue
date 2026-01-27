<template>
    <div class="login-main-container">
        <a-row class="main-container-div">
            <a-col :xs="24" :sm="24" :md="10" :lg="8" class="login-left-column">
                <div class="form-wrapper">
                    <div class="login-logo">
                        <img class="login-img-logo" :src="globalSetting.light_logo_url" alt="logo" />
                    </div>

                    <div class="login-header">
                        <h1>Welcome back</h1>
                        <p>Please enter your details to sign in.</p>
                    </div>

                    <a-alert v-if="onRequestSend.error" :message="onRequestSend.error" type="error" show-icon class="mb-20" />
                    <a-alert v-if="onRequestSend.success" :message="$t('messages.login_success')" type="success" show-icon class="mb-20" />

                    <a-form layout="vertical" class="modern-form">
                        <a-form-item :label="$t('user.email_phone')" name="email"
                            :help="rules.email ? rules.email.message : null"
                            :validateStatus="rules.email ? 'error' : null">
                            <a-input size="large" v-model:value="credentials.email" @pressEnter="onSubmit"
                                :placeholder="$t('common.placeholder_default_text', [$t('user.email_phone')])" />
                        </a-form-item>

                        <a-form-item :label="$t('user.password')" name="password"
                            :help="rules.password ? rules.password.message : null"
                            :validateStatus="rules.password ? 'error' : null">
                            <a-input-password size="large" v-model:value="credentials.password" @pressEnter="onSubmit"
                                :placeholder="$t('common.placeholder_default_text', [$t('user.password')])" />
                        </a-form-item>

                        <div class="flex-justify-between mb-24">
                            <a-checkbox>Remember me</a-checkbox>
                            <router-link :to="{ name: 'admin.forgot_password' }" class="forgot-link">
                                {{ $t('login.forgot_password') }}?
                            </router-link>
                        </div>

                        <a-button :loading="loading" @click="onSubmit" class="login-btn" block type="primary" size="large">
                            {{ $t("menu.login") }}
                        </a-button>

                        <a-divider plain><span class="divider-text">Or continue with</span></a-divider>

                        <a-button :disabled="true" block :loading="isGoogleLoading" @click="handleGoogleLogin" class="google-btn" size="large">
                            <template #icon><google-outlined /></template>
                            Google
                        </a-button>

                        <!-- <div class="login-footer-text">
                            {{ $t('user.dont_have_account') }}
                            <router-link :to="{ name: 'register' }">
                                {{ $t('user.create_account') }}
                            </router-link>
                        </div> -->
                    </a-form>
                </div>
            </a-col>

            <a-col :xs="0" :sm="0" :md="14" :lg="16">
                <div class="right-visual-container" :style="{ backgroundImage: `url(${loginBackground})` }">
                    <div class="overlay-content">
                        <h2>Streamline your workflow.</h2>
                        <p>Join thousands of users managing their business effortlessly.</p>
                    </div>
                </div>
            </a-col>
        </a-row>
    </div>
</template>
<script>
import { defineComponent, reactive, ref } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";

export default defineComponent({
    setup() {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { globalSetting, appType } = common();
        const loginBackground = globalSetting.value.login_image_url;
        const store = useStore();
        const router = useRouter();
        const credentials = reactive({
            email: null,
            password: null,
        });
        const onRequestSend = ref({
            error: "",
            success: "",
        });

        const onSubmit = () => {
            onRequestSend.value = {
                error: "",
                success: false,
            };

            addEditRequestAdmin({
                url: "auth/login",
                data: credentials,
                success: (response) => {
                    const user = response.user;
                    store.commit("auth/updateUser", user);
                    store.commit("auth/updateToken", response.token);
                    store.commit("auth/updateExpires", response.expires_in);
                    store.commit(
                        "auth/updateVisibleSubscriptionModules",
                        response.visible_subscription_modules
                    );

                    if (appType == "non-saas") {
                        store.dispatch("auth/updateAllWarehouses");
                        store.commit("auth/updateWarehouse", response.user.warehouse);

                        router.push({
                            name: "admin.dashboard.index",
                            params: { success: true },
                        });
                    } else {
                        if (user.is_superadmin && user.user_type == "super_admins") {
                            store.commit("auth/updateApp", response.app);
                            store.commit(
                                "auth/updateEmailVerifiedSetting",
                                response.email_setting_verified
                            );
                            router.push({
                                name: "superadmin.dashboard.index",
                                params: { success: true },
                            });
                        } else {
                            store.commit("auth/updateApp", response.app);
                            store.commit(
                                "auth/updateEmailVerifiedSetting",
                                response.email_setting_verified
                            );
                            store.commit(
                                "auth/updateAddMenus",
                                response.shortcut_menus.credentials
                            );
                            store.dispatch("auth/updateAllWarehouses");
                            store.commit("auth/updateWarehouse", response.user.warehouse);
                            router.push({
                                name: "admin.dashboard.index",
                                params: { success: true },
                            });
                        }
                    }
                },
                error: (err) => {
                    onRequestSend.value = {
                        error: err.error.message ? err.error.message : "",
                        success: false,
                    };
                },
            });
        };
        const handleGoogleLogin = () => {
           const googleAuthUrl = `/auth/google/redirect`;
            window.location.href = googleAuthUrl;
        };
        return {
            loading,
            rules,
            credentials,
            onSubmit,
            onRequestSend,
            globalSetting,
            handleGoogleLogin,
            loginBackground,

            innerWidth: window.innerWidth,
        };
    },
});
</script>

<style lang="less" scoped>
.login-main-container {
    background: #ffffff;
    height: 100vh;
    overflow: hidden;
}

.main-container-div {
    height: 100%;
}

.login-left-column {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px;
    background: #fff;
}

.form-wrapper {
    width: 100%;
    max-width: 400px;
}

.login-logo {
    margin-bottom: 40px;
    img { height: 45px; object-fit: contain; }
}

.login-header {
    margin-bottom: 32px;
    h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 8px;
    }
    p {
        color: #8c8c8c;
        font-size: 16px;
    }
}

.modern-form {
    .ant-input-affix-wrapper, .ant-input {
        padding: 10px 15px;
        border-radius: 8px;
    }
}

.flex-justify-between {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.login-btn {
    height: 48px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    box-shadow: 0 4px 12px rgba(24, 144, 255, 0.25);
}

.google-btn {
    height: 48px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #d9d9d9;
    font-weight: 500;
    &:hover {
        background: #fafafa;
        border-color: #d9d9d9;
        color: rgba(0, 0, 0, 0.85);
    }
}

.divider-text {
    color: #bfbfbf;
    font-size: 13px;
}

.login-footer-text {
    margin-top: 24px;
    text-align: center;
    color: #8c8c8c;
    a { font-weight: 600; }
}

/* Right Side Styling */
.right-visual-container {
    height: 100%;
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: flex-end;
    padding: 60px;

    &::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.6) 100%);
    }

    .overlay-content {
        position: relative;
        z-index: 1;
        color: white;
        max-width: 500px;

        h2 {
            color: white;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        p {
            font-size: 18px;
            opacity: 0.9;
        }
    }
}

.mb-20 { margin-bottom: 20px; }
.mb-24 { margin-bottom: 24px; }
</style>
