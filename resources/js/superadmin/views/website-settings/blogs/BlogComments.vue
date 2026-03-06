<template>
    <div>
        <a-row :gutter="16" class="mb-20">
            <a-col :span="12">
                <h3>{{ blog.title }}</h3>
            </a-col>
            <a-col :span="12" style="text-align: right">
                <a-select v-model:value="statusFilter" style="width: 150px" @change="fetchComments">
                    <a-select-option value="all">All</a-select-option>
                    <a-select-option value="pending">Pending</a-select-option>
                    <a-select-option value="approved">Approved</a-select-option>
                    <a-select-option value="rejected">Rejected</a-select-option>
                </a-select>
            </a-col>
        </a-row>

        <a-spin :spinning="loading">
            <div v-if="comments.length === 0" style="text-align: center; padding: 40px; color: #999">
                No comments found.
            </div>

            <a-list :data-source="comments" item-layout="vertical">
                <template #renderItem="{ item }">
                    <a-list-item>
                        <a-list-item-meta>
                            <template #avatar>
                                <a-avatar style="background: #2563eb">
                                    {{ item.name.charAt(0).toUpperCase() }}
                                </a-avatar>
                            </template>
                            <template #title>
                                <span>{{ item.name }}</span>
                                <a-tag
                                    :color="statusColor(item.status)"
                                    style="margin-left: 8px"
                                >
                                    {{ item.status }}
                                </a-tag>
                            </template>
                            <template #description>
                                {{ item.email }} &middot; {{ formatDate(item.created_at) }}
                            </template>
                        </a-list-item-meta>

                        <p>{{ item.comment }}</p>

                        <template #actions>
                            <a-space>
                                <a-button
                                    v-if="item.status !== 'approved'"
                                    size="small"
                                    type="primary"
                                    @click="updateStatus(item, 'approved')"
                                >
                                    <CheckOutlined /> Approve
                                </a-button>
                                <a-button
                                    v-if="item.status !== 'rejected'"
                                    size="small"
                                    danger
                                    @click="updateStatus(item, 'rejected')"
                                >
                                    <CloseOutlined /> Reject
                                </a-button>
                                <a-popconfirm
                                    title="Delete this comment?"
                                    @confirm="deleteComment(item)"
                                >
                                    <a-button size="small">
                                        <DeleteOutlined /> Delete
                                    </a-button>
                                </a-popconfirm>
                            </a-space>
                        </template>
                    </a-list-item>
                </template>
            </a-list>
        </a-spin>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { CheckOutlined, CloseOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import { notification } from "ant-design-vue";

export default {
    components: { CheckOutlined, CloseOutlined, DeleteOutlined },
    props: {
        blog: { type: Object, required: true },
    },
    setup(props) {
        const comments = ref([]);
        const loading = ref(false);
        const statusFilter = ref("all");

        const fetchComments = () => {
            loading.value = true;
            const params = { status: statusFilter.value };
            axiosAdmin
                .get(`superadmin/blogs/${props.blog.id}/comments`, { params })
                .then((response) => {
                    comments.value = response.comments.data;
                })
                .finally(() => {
                    loading.value = false;
                });
        };

        const updateStatus = (comment, status) => {
            axiosAdmin
                .post(`superadmin/blogs/comments/${comment.id}/status`, { status })
                .then(() => {
                    notification.success({
                        message: "Success",
                        description: `Comment ${status}`,
                    });
                    fetchComments();
                });
        };

        const deleteComment = (comment) => {
            axiosAdmin.delete(`superadmin/blogs/comments/${comment.id}`).then(() => {
                notification.success({
                    message: "Success",
                    description: "Comment deleted",
                });
                fetchComments();
            });
        };

        const statusColor = (status) => {
            const colors = { pending: "orange", approved: "green", rejected: "red" };
            return colors[status] || "default";
        };

        const formatDate = (dateStr) => {
            if (!dateStr) return "-";
            return new Date(dateStr).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });
        };

        onMounted(() => {
            fetchComments();
        });

        return {
            comments,
            loading,
            statusFilter,
            fetchComments,
            updateStatus,
            deleteComment,
            statusColor,
            formatDate,
        };
    },
};
</script>
