<template>
    <SuperAdminPageHeader>
        <template #header>
            <a-page-header title="Blog Management" class="p-0">
                <template #extra>
                    <a-button type="primary" @click="showAddForm">
                        <PlusOutlined />
                        New Blog Post
                    </a-button>
                </template>
            </a-page-header>
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        Dashboard
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    Website Settings
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    Blog Management
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </SuperAdminPageHeader>

    <a-row>
        <a-col :xs="24" :sm="24" :md="24" :lg="4" :xl="4" class="bg-setting-sidebar">
            <WebsiteSettingSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-table-content>
                <a-card class="page-content-container mt-20 mb-20" :bodyStyle="{ paddingTop: '15px' }">
                    <a-row :gutter="16" class="mb-20">
                        <a-col :span="8">
                            <a-input-search
                                v-model:value="searchText"
                                placeholder="Search blogs..."
                                @search="fetchBlogs"
                                allowClear
                            />
                        </a-col>
                        <a-col :span="6">
                            <a-select v-model:value="statusFilter" style="width: 100%" @change="fetchBlogs">
                                <a-select-option value="all">All Status</a-select-option>
                                <a-select-option value="published">Published</a-select-option>
                                <a-select-option value="draft">Draft</a-select-option>
                            </a-select>
                        </a-col>
                    </a-row>

                    <a-table
                        :columns="columns"
                        :data-source="blogs"
                        :loading="loading"
                        :pagination="pagination"
                        @change="handleTableChange"
                        rowKey="id"
                    >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.dataIndex === 'featured_image'">
                                <a-avatar
                                    v-if="record.featured_image_url"
                                    :src="record.featured_image_url"
                                    shape="square"
                                    :size="48"
                                />
                                <a-avatar v-else shape="square" :size="48">
                                    <template #icon><FileImageOutlined /></template>
                                </a-avatar>
                            </template>
                            <template v-if="column.dataIndex === 'title'">
                                <strong>{{ record.title }}</strong>
                                <br />
                                <small style="color: #999">{{ record.slug }}</small>
                            </template>
                            <template v-if="column.dataIndex === 'is_published'">
                                <a-tag :color="record.is_published ? 'green' : 'orange'">
                                    {{ record.is_published ? 'Published' : 'Draft' }}
                                </a-tag>
                            </template>
                            <template v-if="column.dataIndex === 'is_featured'">
                                <a-tag v-if="record.is_featured" color="blue">Featured</a-tag>
                                <span v-else>-</span>
                            </template>
                            <template v-if="column.dataIndex === 'published_at'">
                                {{ record.published_at ? formatDate(record.published_at) : '-' }}
                            </template>
                            <template v-if="column.dataIndex === 'actions'">
                                <a-space>
                                    <a-tooltip :title="record.is_published ? 'Unpublish' : 'Publish'">
                                        <a-button
                                            size="small"
                                            :type="record.is_published ? 'default' : 'primary'"
                                            @click="togglePublish(record)"
                                        >
                                            <EyeOutlined v-if="record.is_published" />
                                            <EyeInvisibleOutlined v-else />
                                        </a-button>
                                    </a-tooltip>
                                    <a-tooltip title="Edit">
                                        <a-button size="small" @click="editBlog(record)">
                                            <EditOutlined />
                                        </a-button>
                                    </a-tooltip>
                                    <a-tooltip title="Comments">
                                        <a-button size="small" @click="showComments(record)">
                                            <CommentOutlined />
                                        </a-button>
                                    </a-tooltip>
                                    <a-popconfirm
                                        title="Are you sure you want to delete this blog?"
                                        @confirm="deleteBlog(record)"
                                    >
                                        <a-button size="small" danger>
                                            <DeleteOutlined />
                                        </a-button>
                                    </a-popconfirm>
                                </a-space>
                            </template>
                        </template>
                    </a-table>
                </a-card>
            </admin-page-table-content>
        </a-col>
    </a-row>

    <!-- Add/Edit Modal -->
    <a-drawer
        :title="editingBlog ? 'Edit Blog Post' : 'New Blog Post'"
        :width="800"
        :open="formVisible"
        @close="formVisible = false"
        :destroyOnClose="true"
    >
        <BlogForm
            :blog="editingBlog"
            @saved="onBlogSaved"
            @cancel="formVisible = false"
        />
    </a-drawer>

    <!-- Comments Modal -->
    <a-drawer
        title="Blog Comments"
        :width="700"
        :open="commentsVisible"
        @close="commentsVisible = false"
        :destroyOnClose="true"
    >
        <BlogComments v-if="selectedBlog" :blog="selectedBlog" />
    </a-drawer>
</template>

<script>
import { ref, onMounted, reactive } from "vue";
import {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    EyeOutlined,
    EyeInvisibleOutlined,
    CommentOutlined,
    FileImageOutlined,
} from "@ant-design/icons-vue";
import { notification } from "ant-design-vue";
import SuperAdminPageHeader from "../../../layouts/SuperAdminPageHeader.vue";
import WebsiteSettingSidebar from "../WebsiteSettingSidebar.vue";
import BlogForm from "./BlogForm.vue";
import BlogComments from "./BlogComments.vue";

export default {
    components: {
        PlusOutlined,
        EditOutlined,
        DeleteOutlined,
        EyeOutlined,
        EyeInvisibleOutlined,
        CommentOutlined,
        FileImageOutlined,
        SuperAdminPageHeader,
        WebsiteSettingSidebar,
        BlogForm,
        BlogComments,
    },
    setup() {
        const blogs = ref([]);
        const loading = ref(false);
        const searchText = ref("");
        const statusFilter = ref("all");
        const formVisible = ref(false);
        const commentsVisible = ref(false);
        const editingBlog = ref(null);
        const selectedBlog = ref(null);
        const pagination = reactive({
            current: 1,
            pageSize: 15,
            total: 0,
        });

        const columns = [
            { title: "Image", dataIndex: "featured_image", width: 70 },
            { title: "Title", dataIndex: "title" },
            { title: "Category", dataIndex: "category", width: 120 },
            { title: "Status", dataIndex: "is_published", width: 100 },
            { title: "Featured", dataIndex: "is_featured", width: 90 },
            { title: "Published", dataIndex: "published_at", width: 120 },
            { title: "Actions", dataIndex: "actions", width: 200 },
        ];

        const fetchBlogs = () => {
            loading.value = true;
            const params = {
                page: pagination.current,
                limit: pagination.pageSize,
            };
            if (searchText.value) params.search = searchText.value;
            if (statusFilter.value !== "all") params.status = statusFilter.value;

            axiosAdmin
                .get("superadmin/blogs", { params })
                .then((response) => {
                    blogs.value = response.blogs.data;
                    pagination.total = response.blogs.total;
                    pagination.current = response.blogs.current_page;
                })
                .finally(() => {
                    loading.value = false;
                });
        };

        const handleTableChange = (pag) => {
            pagination.current = pag.current;
            pagination.pageSize = pag.pageSize;
            fetchBlogs();
        };

        const showAddForm = () => {
            editingBlog.value = null;
            formVisible.value = true;
        };

        const editBlog = (blog) => {
            editingBlog.value = blog;
            formVisible.value = true;
        };

        const deleteBlog = (blog) => {
            axiosAdmin.delete(`superadmin/blogs/${blog.id}`).then(() => {
                notification.success({
                    message: "Success",
                    description: "Blog deleted successfully",
                });
                fetchBlogs();
            });
        };

        const togglePublish = (blog) => {
            axiosAdmin.post(`superadmin/blogs/${blog.id}/toggle-publish`).then((response) => {
                notification.success({
                    message: "Success",
                    description: response.blog.is_published ? "Blog published" : "Blog unpublished",
                });
                fetchBlogs();
            });
        };

        const showComments = (blog) => {
            selectedBlog.value = blog;
            commentsVisible.value = true;
        };

        const onBlogSaved = () => {
            formVisible.value = false;
            fetchBlogs();
        };

        const formatDate = (dateStr) => {
            if (!dateStr) return "-";
            const date = new Date(dateStr);
            return date.toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        };

        onMounted(() => {
            fetchBlogs();
        });

        return {
            blogs,
            loading,
            searchText,
            statusFilter,
            formVisible,
            commentsVisible,
            editingBlog,
            selectedBlog,
            columns,
            pagination,
            fetchBlogs,
            handleTableChange,
            showAddForm,
            editBlog,
            deleteBlog,
            togglePublish,
            showComments,
            onBlogSaved,
            formatDate,
        };
    },
};
</script>
