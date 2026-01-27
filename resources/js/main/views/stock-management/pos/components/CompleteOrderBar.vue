<template>
  <a-button
    type="primary"
    block
    size="large"
    :disabled="disabled"
    @click="$emit('complete')"
  >
    Complete Order
  </a-button>
</template>

<script setup>
defineProps({
  disabled: Boolean,
});

defineEmits(["complete"]);
</script>
