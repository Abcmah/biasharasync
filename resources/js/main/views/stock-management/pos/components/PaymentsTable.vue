<template>
  <a-table
    :dataSource="payments"
    :pagination="false"
    rowKey="id"
  >
    <a-table-column title="Method" dataIndex="method" />
    <a-table-column title="Amount" dataIndex="amount" />
    <a-table-column title="Action">
      <template #default="{ record }">
        <a-button danger @click="$emit('remove', record.id)">
          Delete
        </a-button>
      </template>
    </a-table-column>
  </a-table>
</template>

<script setup>
defineProps({
  payments: Array,
});

defineEmits(["remove"]);
</script>
