<template>
    <div class="profile-settings-wrapper">
        <a-row type="flex" justify="center">
            <a-col :xs="22" :sm="22" :md="22" :lg="20">
                <a-breadcrumb class="modern-breadcrumb mt-30">
                    <a-breadcrumb-item><router-link to="/"><home-outlined /> {{ $t("front.home") }}</router-link></a-breadcrumb-item>
                    <a-breadcrumb-item><router-link to="/dashboard">{{ $t("front.dashboard") }}</router-link></a-breadcrumb-item>
                    <a-breadcrumb-item>{{ $t("front.profile") }}</a-breadcrumb-item>
                </a-breadcrumb>

                <a-row :gutter="[32, 32]" class="mt-30 pb-50">
                    <a-col :xs="24" :lg="6">
                        <div class="sticky-sidebar">
                            <dashboard-sidebar />
                        </div>
                    </a-col>

                    <a-col :xs="24" :lg="18">
                        <a-card :bordered="false" class="profile-main-card">
                            <div class="profile-header-strip">
                                <div class="avatar-container">
                                    <a-avatar :size="80" :src="formData.profile_image_url">
                                        <template #icon><UserOutlined /></template>
                                    </a-avatar>
                                    <div class="user-meta">
                                        <h2>{{ formData.name }}</h2>
                                        <span>{{ formData.email }}</span>
                                    </div>
                                </div>
                                <a-tag color="blue">Verified Account</a-tag>
                            </div>

                            <a-divider />

                            <a-form layout="vertical" class="modern-form">
                                <div class="form-section">
                                    <h3 class="section-title">Personal Information</h3>
                                    <a-row :gutter="24">
                                        <a-col :xs="24" :md="12">
                                            <a-form-item :label="$t('user.name')" v-bind="validate('name')">
                                                <a-input v-model:value="formData.name" size="large" />
                                            </a-form-item>
                                        </a-col>
                                        <a-col :xs="24" :md="12">
                                            <a-form-item :label="$t('user.phone')" v-bind="validate('phone')">
                                                <a-input v-model:value="formData.phone" size="large" />
                                            </a-form-item>
                                        </a-col>
                                        <a-col :xs="24" :md="12">
                                            <a-form-item :label="$t('user.email')">
                                                <a-input :value="formData.email" disabled size="large" />
                                            </a-form-item>
                                        </a-col>
                                        <a-col :xs="24" :md="12">
                                            <a-form-item :label="$t('user.password')" v-bind="validate('password')">
                                                <a-input-password v-model:value="formData.password" size="large" />
                                                <div class="helper-text">{{ $t("user.password_blank") }}</div>
                                            </a-form-item>
                                        </a-col>
                                    </a-row>
                                </div>

                                <a-divider />

                                <div class="form-section">
                                    <h3 class="section-title">Profile Picture</h3>
                                    <div class="upload-wrapper-modern">
                                        <UploadFront
                                            :formData="formData"
                                            folder="user"
                                            imageField="profile_image"
                                            @onFileUploaded="handleUpload"
                                        />
                                    </div>
                                </div>

                                <a-divider />

                                <div class="form-section">
                                    <h3 class="section-title">Address Management</h3>
                                    <a-row :gutter="24">
                                        <a-col :xs="24" :md="12">
                                            <a-form-item :label="$t('user.billing_address')" v-bind="validate('address')">
                                                <a-textarea v-model:value="formData.address" :rows="3" />
                                            </a-form-item>
                                        </a-col>
                                        <a-col :xs="24" :md="12">
                                            <a-form-item :label="$t('user.shipping_address')" v-bind="validate('shipping_address')">
                                                <a-textarea v-model:value="formData.shipping_address" :rows="3" />
                                            </a-form-item>
                                        </a-col>
                                    </a-row>
                                </div>

                                <div class="form-footer mt-20">
                                    <a-button type="primary" size="large" @click="onSubmit" class="save-btn">
                                        <template #icon><SaveOutlined /></template>
                                        Save Profile Changes
                                    </a-button>
                                </div>
                            </a-form>
                        </a-card>
                    </a-col>
                </a-row>
            </a-col>
        </a-row>
    </div>
</template>

<style lang="less" scoped>
.profile-settings-wrapper {
    background-color: #f8fafc;
    min-height: 100vh;
}

.sticky-sidebar {
    position: sticky;
    top: 100px;
}

.profile-main-card {
    border-radius: 20px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    padding: 10px;
}

.profile-header-strip {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;

    .avatar-container {
        display: flex;
        align-items: center;
        gap: 20px;

        .user-meta {
            h2 { margin: 0; font-size: 22px; font-weight: 800; color: #0f172a; }
            span { color: #64748b; font-size: 14px; }
        }
    }
}

.section-title {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 20px;
}

.helper-text {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 4px;
}

.modern-form {
    :deep(.ant-input), :deep(.ant-input-password), :deep(.ant-input-affix-wrapper) {
        border-radius: 8px;
    }
}

.save-btn {
    height: 48px;
    padding: 0 40px;
    border-radius: 10px;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(40, 116, 240, 0.39);
}

.upload-wrapper-modern {
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px dashed #e2e8f0;
}

@media (max-width: 768px) {
    .profile-header-strip {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}
</style>

<script>
import { onMounted, ref } from "vue";
import { SaveOutlined, UserOutlined, HomeOutlined } from "@ant-design/icons-vue";
import { notification, message } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
import processRequestFront from "../../../../common/plugins/processRequestFront";
import UploadFront from "../../../../common/core/ui/file/UploadFront.vue";
import DashboardSidebar from "../includes/DashboardSidebar.vue";

export default {
    components: { SaveOutlined, UserOutlined, HomeOutlined, DashboardSidebar, UploadFront },
    setup() {
        const { t } = useI18n();
        const store = useStore();
        const formData = ref({});
        const rules = ref({});
        const user = store.state.front.user;

        onMounted(() => {
            formData.value = { ...user, password: "" };
        });

        // Helper to simplify validation props on form items
        const validate = (field) => ({
            help: rules.value[field] ? rules.value[field].message : null,
            validateStatus: rules.value[field] ? 'error' : null
        });

        const handleUpload = (file) => {
            formData.value.profile_image = file.file;
            formData.value.profile_image_url = file.file_url;
        };

        const onSubmit = () => {
            processRequestFront({
                url: "front/profile",
                data: formData.value,
                success: (res) => {
                    store.commit("front/updateUser", res.user);
                    notification.success({
                        placement: "bottomRight",
                        message: t("common.success"),
                        description: t("user.profile_updated"),
                    });
                    rules.value = {};
                },
                error: (errorRules) => {
                    rules.value = errorRules;
                    message.error(t("common.fix_errors"));
                },
            });
        };

        return { formData, rules, onSubmit, handleUpload, validate };
    },
};
</script>
