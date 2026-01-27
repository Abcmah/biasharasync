<template>
  <a-row :gutter="16" class="mt-20">
    <!-- <a-col :span="8">
      <a-button block type="primary" @click="$emit('select', 'cash')">
        Cash / Card
      </a-button>
    </a-col> -->

    <a-col :span="8">
      <a-button block type="dashed" @click="$emit('mpesa')">
        M-Pesa
      </a-button>
    </a-col>
  </a-row>
</template>

<script setup>
defineEmits(["select", "mpesa"]);
</script>
