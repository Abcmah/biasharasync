<template>
  <AdminPageHeader>
    <template #header>
      <a-page-header :title="$t('menu.payment_providers')" class="p-0" />
    </template>

    <template #breadcrumb>
      <a-breadcrumb separator="-" style="font-size: 12px">
        <a-breadcrumb-item>
          <router-link :to="{ name: 'admin.dashboard.index' }">
            {{ $t("menu.dashboard") }}
          </router-link>
        </a-breadcrumb-item>
        <a-breadcrumb-item>
          {{ $t("menu.settings") }}
        </a-breadcrumb-item>
        <a-breadcrumb-item>
          {{ $t("menu.payment_providers") }}
        </a-breadcrumb-item>
      </a-breadcrumb>
    </template>
  </AdminPageHeader>

  <a-row>
    <a-col :lg="4" class="bg-setting-sidebar">
      <SettingSidebar />
    </a-col>

    <a-col :lg="20">
      <!-- FILTERS -->
      <admin-page-filters>
        <a-row :gutter="[16, 16]">
          <a-col :lg="10">
            <a-space>
              <a-button
                v-if="
                  permsArray.includes('payment_providers_create') ||
                  permsArray.includes('admin')
                "
                type="primary"
                @click="addItem"
              >
                <PlusOutlined />
                {{ $t("payment_provider.add") }}
              </a-button>

              <a-button
                v-if="
                  table.selectedRowKeys.length > 0 &&
                  (permsArray.includes('payment_providers_delete') ||
                    permsArray.includes('admin'))
                "
                danger
                type="primary"
                @click="showSelectedDeleteConfirm"
              >
                <DeleteOutlined />
                {{ $t("common.delete") }}
              </a-button>
            </a-space>
          </a-col>

          <a-col :lg="14">
            <a-row justify="end">
              <a-col :lg="12">
                <a-input-group compact>
                  <a-select
                    style="width: 30%"
                    v-model:value="table.searchColumn"
                  >
                    <a-select-option
                      v-for="col in filterableColumns"
                      :key="col.key"
                      :value="col.key"
                    >
                      {{ col.value }}
                    </a-select-option>
                  </a-select>

                  <a-input-search
                    style="width: 70%"
                    v-model:value="table.searchString"
                    @search="onTableSearch"
                    @change="onTableSearch"
                    :loading="table.filterLoading"
                  />
                </a-input-group>
              </a-col>
            </a-row>
          </a-col>
        </a-row>
      </admin-page-filters>

      <!-- TABLE -->
      <admin-page-table-content>
        <AddEdit
          :addEditType="addEditType"
          :visible="addEditVisible"
          :url="addEditUrl"
          :formData="formData"
          :data="viewData"
          :pageTitle="pageTitle"
          :successMessage="successMessage"
          @addEditSuccess="addEditSuccess"
          @closed="onCloseAddEdit"
        />

        <a-table
          bordered
          size="middle"
          :columns="columns"
          :data-source="table.data"
          :loading="table.loading"
          :pagination="table.pagination"
          :row-key="record => record.xid"
          :row-selection="{
            selectedRowKeys: table.selectedRowKeys,
            onChange: onRowSelectChange,
          }"
          @change="handleTableChange"
        >
          <template #bodyCell="{ column, record }">
             <template v-if="column.dataIndex === 'payment_mode_id'">
             {{ record.payment_mode.name }}
            </template>
            <!-- ENV -->
            <template v-if="column.dataIndex === 'env'">
              <a-tag :color="record.env === 'live' ? 'green' : 'orange'">
                {{ record.env.toUpperCase() }}
              </a-tag>
            </template>

            <!-- ACTIVE -->
            <template v-if="column.dataIndex === 'is_active'">
              <a-tag :color="record.is_active ? 'green' : 'red'">
                {{ record.is_active ? $t("common.active") : $t("common.inactive") }}
              </a-tag>
            </template>

            <!-- ACTIONS -->
            <template v-if="column.dataIndex === 'action'">
              <a-button
                v-if="
                  permsArray.includes('payment_providers_edit') ||
                  permsArray.includes('admin')
                "
                type="primary"
                @click="editItem(record)"
              >
                <EditOutlined />
              </a-button>

              <a-button
                v-if="
                  permsArray.includes('payment_providers_delete') ||
                  permsArray.includes('admin')
                "
                danger
                type="primary"
                style="margin-left: 6px"
                @click="showDeleteConfirm(record.xid)"
              >
                <DeleteOutlined />
              </a-button>
            </template>
          </template>
        </a-table>
      </admin-page-table-content>
    </a-col>
  </a-row>
</template>

<script>
import { onMounted } from "vue";
import { PlusOutlined, EditOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import crud from "../../../../common/composable/crud";
import common from "../../../../common/composable/common";
import fields from "./fields";
import AddEdit from "./AddEdit.vue";
import SettingSidebar from "../SettingSidebar.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";

export default {
  components: {
    PlusOutlined,
    EditOutlined,
    DeleteOutlined,
    AddEdit,
    SettingSidebar,
    AdminPageHeader,
  },

  setup() {
    const { permsArray } = common();
    const { url, addEditUrl, initData, columns, filterableColumns } = fields();
    const crudVariables = crud();

    onMounted(() => {
      crudVariables.tableUrl.value = { url };
      crudVariables.table.filterableColumns = filterableColumns;

      crudVariables.fetch({ page: 1 });

      crudVariables.crudUrl.value = addEditUrl;
      crudVariables.langKey.value = "payment_provider";
      crudVariables.initData.value = { ...initData };
      crudVariables.formData.value = { ...initData };
    });

    return {
      permsArray,
      columns,
      filterableColumns,
      ...crudVariables,
    };
  },
};
</script>
