<template>
    <a-form layout="vertical" @finish="onSubmit">
        <a-form-item label="Title" :validateStatus="errors.title ? 'error' : ''" :help="errors.title">
            <a-input v-model:value="form.title" placeholder="Enter blog title" />
        </a-form-item>

        <a-row :gutter="16">
            <a-col :span="12">
                <a-form-item label="Category">
                    <a-input v-model:value="form.category" placeholder="e.g. Feature Guide, News" />
                </a-form-item>
            </a-col>
            <a-col :span="12">
                <a-form-item label="Author Name">
                    <a-input v-model:value="form.author_name" placeholder="Author name" />
                </a-form-item>
            </a-col>
        </a-row>

        <a-form-item label="Excerpt">
            <a-textarea v-model:value="form.excerpt" :rows="3" placeholder="Short summary of the blog post" />
        </a-form-item>

        <a-form-item label="Content" :validateStatus="errors.content ? 'error' : ''" :help="errors.content">
            <a-textarea v-model:value="form.content" :rows="12" placeholder="Blog content (HTML supported)" />
        </a-form-item>

        <a-form-item label="Featured Image">
            <a-upload
                :before-upload="handleImageUpload"
                :show-upload-list="false"
                accept="image/*"
            >
                <a-button>
                    <UploadOutlined /> Select Image
                </a-button>
            </a-upload>
            <div v-if="imagePreview || (blog && blog.featured_image_url)" class="mt-10">
                <img
                    :src="imagePreview || blog.featured_image_url"
                    style="max-width: 200px; max-height: 120px; border-radius: 8px; object-fit: cover;"
                />
                <br />
                <a-button size="small" danger class="mt-5" @click="removeImage">Remove</a-button>
            </div>
        </a-form-item>

        <a-row :gutter="16">
            <a-col :span="12">
                <a-form-item label="Published">
                    <a-switch v-model:checked="form.is_published" />
                </a-form-item>
            </a-col>
            <a-col :span="12">
                <a-form-item label="Featured">
                    <a-switch v-model:checked="form.is_featured" />
                </a-form-item>
            </a-col>
        </a-row>

        <a-collapse class="mb-20">
            <a-collapse-panel key="seo" header="SEO Settings">
                <a-form-item label="SEO Title">
                    <a-input v-model:value="form.seo_title" placeholder="SEO title (optional)" />
                </a-form-item>
                <a-form-item label="SEO Description">
                    <a-textarea v-model:value="form.seo_description" :rows="2" placeholder="SEO description (optional)" />
                </a-form-item>
                <a-form-item label="SEO Keywords">
                    <a-input v-model:value="form.seo_keywords" placeholder="Comma separated keywords" />
                </a-form-item>
            </a-collapse-panel>
        </a-collapse>

        <a-space>
            <a-button @click="onSubmit" type="primary" html-type="submit" :loading="submitting">
                {{ blog ? 'Update Blog' : 'Create Blog' }}
            </a-button>
            <a-button @click="$emit('cancel')">Cancel</a-button>
        </a-space>
    </a-form>
</template>

<script>
import { ref, reactive, onMounted } from "vue";
import { UploadOutlined } from "@ant-design/icons-vue";
import { notification } from "ant-design-vue";

export default {
    components: { UploadOutlined },
    props: {
        blog: { type: Object, default: null },
    },
    emits: ["saved", "cancel"],
    setup(props, { emit }) {
        const submitting = ref(false);
        const errors = reactive({});
        const imageFile = ref(null);
        const imagePreview = ref(null);

        const form = reactive({
            title: "",
            category: "",
            excerpt: "",
            content: "",
            author_name: "",
            is_published: false,
            is_featured: false,
            seo_title: "",
            seo_description: "",
            seo_keywords: "",
        });

        onMounted(() => {
            if (props.blog) {
                Object.assign(form, {
                    title: props.blog.title || "",
                    category: props.blog.category || "",
                    excerpt: props.blog.excerpt || "",
                    content: props.blog.content || "",
                    author_name: props.blog.author_name || "",
                    is_published: props.blog.is_published || false,
                    is_featured: props.blog.is_featured || false,
                    seo_title: props.blog.seo_title || "",
                    seo_description: props.blog.seo_description || "",
                    seo_keywords: props.blog.seo_keywords || "",
                });
            }
        });

        const handleImageUpload = (file) => {
            imageFile.value = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.value = e.target.result;
            };
            reader.readAsDataURL(file);
            return false;
        };

        const removeImage = () => {
            imageFile.value = null;
            imagePreview.value = null;
        };

        const onSubmit = () => {
            alert('sdsd')
            // Clear errors
            Object.keys(errors).forEach((key) => delete errors[key]);

            if (!form.title) {
                errors.title = "Title is required";
                return;
            }
            if (!form.content) {
                errors.content = "Content is required";
                return;
            }

            submitting.value = true;

            const formData = new FormData();
            formData.append("title", form.title);
            formData.append("content", form.content);
            formData.append("category", form.category || "");
            formData.append("excerpt", form.excerpt || "");
            formData.append("author_name", form.author_name || "");
            formData.append("is_published", form.is_published ? "1" : "0");
            formData.append("is_featured", form.is_featured ? "1" : "0");
            formData.append("seo_title", form.seo_title || "");
            formData.append("seo_description", form.seo_description || "");
            formData.append("seo_keywords", form.seo_keywords || "");

            if (imageFile.value) {
                formData.append("featured_image", imageFile.value);
            }

            const url = props.blog
                ? `superadmin/blogs/${props.blog.id}`
                : "superadmin/blogs";

            axiosAdmin
                .post(url, formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                })
                .then(() => {
                    notification.success({
                        message: "Success",
                        description: props.blog
                            ? "Blog updated successfully"
                            : "Blog created successfully",
                    });
                    emit("saved");
                })
                .catch((err) => {
                    if (err.status === 422 && err.data?.errors) {
                        const details = err.data.errors;
                        Object.keys(details).forEach((key) => {
                            errors[key] = details[key][0];
                        });
                    } else {
                        notification.error({
                            message: "Error",
                            description: "Something went wrong",
                        });
                    }
                })
                .finally(() => {
                    submitting.value = false;
                });
        };

        return {
            form,
            errors,
            submitting,
            imageFile,
            imagePreview,
            handleImageUpload,
            removeImage,
            onSubmit,
        };
    },
};
</script>
