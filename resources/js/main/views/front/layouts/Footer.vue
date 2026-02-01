<template>
    <footer class="modern-footer" v-if="frontAppSetting?.footer_company_description">
        <div class="footer-container">
            <a-row :gutter="[40, 40]">
                <a-col :xs="24" :lg="9">
                    <div class="brand-section">
                        <img :src="frontWarehouse.dark_logo_url" class="footer-logo" alt="Logo" />
                        <p class="company-bio">
                            {{ frontAppSetting.footer_company_description }}
                        </p>

                        <div class="newsletter-box">
                            <h4 class="newsletter-title">Stay in the loop</h4>
                            <div class="newsletter-form">
                                <input
                                    type="email"
                                    placeholder="your@email.com"
                                    v-model="newsletterEmail"
                                />
                                <button @click="handleSubscribe" :class="{ 'is-success': isSubscribed }">
                                    <SendOutlined v-if="!isSubscribed" />
                                    <CheckOutlined v-else />
                                </button>
                            </div>
                        </div>

                        <div class="social-links">
                            <a v-if="frontAppSetting.facebook_url" :href="frontAppSetting.facebook_url" class="social-btn facebook"><FacebookFilled /></a>
                            <a v-if="frontAppSetting.instagram_url" :href="frontAppSetting.instagram_url" class="social-btn instagram"><InstagramFilled /></a>
                            <a v-if="frontAppSetting.twitter_url" :href="frontAppSetting.twitter_url" class="social-btn twitter"><TwitterCircleFilled /></a>
                            <a v-if="frontAppSetting.linkedin_url" :href="frontAppSetting.linkedin_url" class="social-btn linkedin"><LinkedinFilled /></a>
                            <a v-if="frontAppSetting.youtube_url" :href="frontAppSetting.youtube_url" class="social-btn youtube"><YoutubeFilled /></a>
                        </div>
                    </div>
                </a-col>

                <a-col :xs="24" :lg="15">
                    <a-row :gutter="[20, 40]">
                        <a-col :xs="12" :sm="8">
                            <h4 class="footer-heading">{{ $t("front_setting.useful_links") }}</h4>
                            <ul class="link-list">
                                <li v-for="(item, index) in frontAppSetting.links_widget" :key="index">
                                    <a :href="item.value" target="_blank">{{ item.title }}</a>
                                </li>
                            </ul>
                        </a-col>
                        <a-col :xs="12" :sm="8">
                            <h4 class="footer-heading">{{ $t("front_setting.pages") }}</h4>
                            <ul class="link-list">
                                <li v-for="(item, index) in frontAppSetting.pages_widget" :key="index">
                                    <a :href="item.value" target="_blank">{{ item.title }}</a>
                                </li>
                            </ul>
                        </a-col>
                        <a-col :xs="24" :sm="8">
                            <h4 class="footer-heading">{{ $t("front_setting.contact") }}</h4>
                            <div class="contact-info">
                                <div v-for="(item, index) in frontAppSetting.contact_info_widget" :key="index" class="contact-item">
                                    <span class="contact-label">{{ item.title }}</span>
                                    <span class="contact-text">{{ item.value }}</span>
                                </div>
                            </div>
                        </a-col>
                    </a-row>
                </a-col>
            </a-row>
        </div>

        <div class="footer-copyright-bar">
            <div class="footer-container">
                <div class="copyright-flex">
                    <p>{{ frontAppSetting.footer_copyright_text }}</p>
                    <div class="security-badges">
                        <span>Secure Payments</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
import { ref } from "vue";
import {
    FacebookFilled,
    TwitterCircleFilled,
    LinkedinFilled,
    InstagramFilled,
    YoutubeFilled,
    SendOutlined,
    CheckOutlined
} from "@ant-design/icons-vue";
import cart from "../../../../common/composable/cart";

export default {
    props: ["warehouse"],
    components: {
        FacebookFilled,
        LinkedinFilled,
        InstagramFilled,
        SendOutlined,
        CheckOutlined,
        YoutubeFilled,
        TwitterCircleFilled,
    },
    setup() {
        const { frontAppSetting, frontWarehouse } = cart();
        const newsletterEmail = ref("");
        const isSubscribed = ref(false);

        const handleSubscribe = () => {
            if (newsletterEmail.value) {
                isSubscribed.value = true;
                setTimeout(() => {
                    isSubscribed.value = false;
                    newsletterEmail.value = "";
                }, 3000);
            }
        };

        return {
            frontAppSetting,
            newsletterEmail,
            isSubscribed,
            handleSubscribe,
            frontWarehouse,
        };
    },
};
</script>

<style lang="less" scoped>
.modern-footer {
    background: #0f172a;
    color: #94a3b8;
    padding-top: 70px;
    border-top: 1px solid rgba(255, 255, 255, 0.05);

    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Brand & Newsletter Section */
    .brand-section {
        .footer-logo {
            height: 42px;
            margin-bottom: 24px;
            filter: brightness(0) invert(1);
        }
        .company-bio {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
    }

    .newsletter-box {
        background: rgba(255, 255, 255, 0.03);
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        border: 1px solid rgba(255, 255, 255, 0.05);

        .newsletter-title {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
        }
    }

    .newsletter-form {
        display: flex;
        gap: 8px;
        input {
            flex: 1;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 8px 12px;
            color: #fff;
            outline: none;
            font-size: 13px;
            &::placeholder { color: #475569; }
            &:focus { border-color: #3b82f6; }
        }
        button {
            background: #3b82f6;
            color: #fff;
            border: none;
            padding: 0 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            &.is-success { background: #22c55e; }
            &:hover { opacity: 0.9; transform: scale(1.05); }
        }
    }

    /* Social Icons */
    .social-links {
        display: flex;
        gap: 10px;
        .social-btn {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.3s;
            &:hover {
                color: #fff;
                transform: translateY(-3px);
                &.facebook { background: #1877f2; }
                &.instagram { background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%,#d6249f 60%,#285AEB 90%); }
                &.twitter { background: #000; }
                &.linkedin { background: #0077b5; }
                &.youtube { background: #ff0000; }
            }
        }
    }

    /* Links Columns */
    .footer-heading {
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 25px;
    }

    .link-list {
        list-style: none;
        padding: 0;
        li {
            margin-bottom: 12px;
            a {
                color: #94a3b8;
                font-size: 14px;
                transition: color 0.2s;
                &:hover { color: #fff; }
            }
        }
    }

    /* Contact Info */
    .contact-item {
        margin-bottom: 15px;
        .contact-label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }
        .contact-text {
            color: #e2e8f0;
            font-size: 14px;
        }
    }

    /* Copyright Bar */
    .footer-copyright-bar {
        margin-top: 60px;
        padding: 25px 0;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        .copyright-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            p { margin: 0; }
        }
    }
}

@media (max-width: 991px) {
    .modern-footer {
        text-align: center;
        .social-links { justify-content: center; }
        .newsletter-form { max-width: 400px; margin: 0 auto; }
        .copyright-flex { flex-direction: column; gap: 10px; }
    }
}
</style>
