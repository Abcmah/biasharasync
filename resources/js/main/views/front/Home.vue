<template>
    <div class="homepage-modern">
        <a-row type="flex" justify="center">
            <a-col :xs="22" :sm="22" :md="22" :lg="20">

                <div class="hero-banners mt-30">
                    <a-row :gutter="[20, 20]">
                        <a-col :xs="24" :md="14" :lg="16">
                            <div class="hero-card main-carousel">
                                <a-carousel autoplay effect="fade">
                                    <div v-for="item in frontSettings.bottom_banners_1_details" :key="item.uid">
                                        <div class="banner-slide" :style="{ backgroundImage: `url(${item.url})` }">
                                            <div class="banner-overlay"></div>
                                        </div>
                                    </div>
                                </a-carousel>
                            </div>
                        </a-col>
                        <a-col :xs="24" :md="10" :lg="8">
                            <a-row :gutter="[0, 20]">
                                <a-col :span="24">
                                    <div class="hero-card sub-banner">
                                        <a-carousel autoplay dots-class="slick-dots custom-dots">
                                            <div v-for="item in frontSettings.bottom_banners_2_details" :key="item.uid">
                                                <img :src="item.url" class="side-img" />
                                            </div>
                                        </a-carousel>
                                    </div>
                                </a-col>
                                <a-col :span="24">
                                    <div class="hero-card sub-banner static-promo">
                                        <div v-for="item in frontSettings.bottom_banners_3_details" :key="item.uid">
                                            <img :src="item.url" class="side-img" />
                                        </div>
                                    </div>
                                </a-col>
                            </a-row>
                        </a-col>
                    </a-row>
                </div>

                <section class="section-padding" v-if="frontSettings.featured_categories_details?.length">
                    <div class="section-header">
                        <h2>{{ frontSettings.featured_categories_title }}</h2>
                        <p>{{ frontSettings.featured_categories_subtitle }}</p>
                    </div>
                    <div class="category-grid">
                        <div
                            v-for="item in frontSettings.featured_categories_details"
                            :key="item.id"
                            class="category-bubble-card"
                        >
                            <div class="bubble-img">
                                <img :src="item.image_url" />
                            </div>
                            <span>{{ item.name }}</span>
                        </div>
                    </div>
                </section>

                <section class="section-padding bg-light-section" v-if="featuredProducts.length">
                    <div class="section-header">
                        <h2>{{ frontSettings.featured_products_title }}</h2>
                        <div class="header-line">dd</div>
                    </div>
                    <a-row :gutter="[24, 24]">
                        <a-col v-for="product in featuredProducts" :key="product.id" :xs="24" :sm="24" :md="8" :lg="6" :xl="4">
                            <ProductCard  :product="product" :currency="currency" />
                        </a-col>
                    </a-row>
                </section>

                <div v-for="frontProductCard in frontProductCards" :key="frontProductCard.id" class="section-padding">
                    <div class="section-header flex-header">
                        <div class="text-group">
                            <h2>{{ frontProductCard.title }}</h2>
                            <p v-if="frontProductCard.subtitle">{{ frontProductCard.subtitle }}</p>
                        </div>
                        <a-button type="link" class="view-all-btn">
                            View All <right-outlined />
                        </a-button>
                    </div>
                    <a-row :gutter="[24, 24]">
                        <a-col v-for="product in frontProductCard.products_details" :key="product.id" :xs="12" :sm="12" :md="8" :lg="6" :xl="4">
                            <ProductCard :product="product" :currency="currency" />
                        </a-col>
                    </a-row>
                </div>

                <div class="bottom-hero mt-50 mb-50">
                    <a-carousel autoplay arrows>
                        <div v-for="item in frontSettings.top_banners_details" :key="item.uid">
                            <div class="full-width-banner" :style="{ backgroundImage: `url(${item.url})` }"></div>
                        </div>
                    </a-carousel>
                </div>

            </a-col>
        </a-row>
    </div>
</template>
<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    RightOutlined,
    RightCircleOutlined,
    LeftCircleOutlined,
} from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import ProductCard from "./components/ProductCard.vue";
import CategoryHeader from "./includes/CategoryHeader.vue";
import common from "../../../common/composable/common";

export default defineComponent({
    components: {
        RightOutlined,
        RightCircleOutlined,
        LeftCircleOutlined,
        ProductCard,
        CategoryHeader,
    },
    setup() {
        const { frontWarehouse } = common();
        const route = useRoute();
        const frontSettings = ref({});
        const frontProductCards = ref([]);
        const featuredProducts = ref([]);
        const currency = ref({});

        onMounted(() => {
            axiosFront
                .get(`front/homepage/${frontWarehouse.value.slug}`)
                .then((response) => {
                    currency.value = response.data.currency;
                    frontSettings.value = response.data.front_settings;
                    frontProductCards.value = response.data.front_product_cards;
                    featuredProducts.value =
                        frontSettings.value.featured_products_details;
                });
        });

        return {
            frontSettings,
            frontProductCards,
            featuredProducts,
            currency,
        };
    },
});
</script>

<style lang="less" scoped>
.homepage-modern {
    background: #ffffff;

    .section-padding {
        padding: 60px 0;
    }

    /* Hero Banner Grid */
    .hero-card {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);

        .banner-slide {
            height: 440px;
            background-size: cover;
            background-position: center;
        }

        .side-img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            display: block;
        }
    }

    /* Section Headers */
    .section-header {
        text-align: center;
        margin-bottom: 40px;

        h2 {
            font-size: 32px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
        }
        p {
            font-size: 16px;
            color: #64748b;
        }

        &.flex-header {
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 15px;
        }
    }

    /* Category Bubbles */
    .category-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 30px;

        .category-bubble-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            width: 120px;

            .bubble-img {
                width: 100px;
                height: 100px;
                background: #f8fafc;
                border-radius: 50%;
                padding: 15px;
                margin-bottom: 12px;
                transition: all 0.3s ease;
                border: 1px solid #f1f5f9;

                img { width: 100%; height: 100%; object-fit: contain; }
            }

            span {
                font-weight: 600;
                color: #334155;
                font-size: 14px;
                text-align: center;
            }

            &:hover .bubble-img {
                transform: translateY(-5px);
                background: #fff;
                box-shadow: 0 10px 20px rgba(40, 116, 240, 0.1);
                border-color: #2874f0;
            }
        }
    }

    /* Customizing Carousel Dots */
    :deep(.slick-dots) {
        bottom: 20px;
        li button { background: #fff; opacity: 0.5; }
        li.slick-active button { background: #fff; opacity: 1; }
    }

    .full-width-banner {
        height: 350px;
        background-size: cover;
        background-position: center;
        border-radius: 24px;
    }
}

@media (max-width: 768px) {
    .section-padding { padding: 40px 0; }
    .hero-card .banner-slide { height: 250px; }
    .hero-card .side-img { height: 150px; }
    .section-header h2 { font-size: 24px; }

    // Grid 2 columns on mobile
    :deep(.ant-col-xs-12) { width: 50% !important; }
}
</style>
