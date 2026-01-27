<template>
  <a-col :md="8">
    <a-statistic title="Items" :value="selectedProducts.length" />
    <a-statistic class="mt-20" title="Payable" :value="format(subtotal)" />
    <a-statistic
      class="mt-20"
      :title="totalEnteredAmount < subtotal ? 'Due' : 'Change'"
      :value="format(Math.abs(subtotal - totalEnteredAmount))"
    />
  </a-col>
</template>

<script setup>
import common from "@/common/composable/common";

defineProps({
  subtotal: Number,
  selectedProducts: Array,
  totalEnteredAmount: Number,
});

const { formatAmountCurrency: format } = common();
</script>
