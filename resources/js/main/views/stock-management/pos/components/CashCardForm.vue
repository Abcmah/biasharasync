<template>
  <a-form layout="vertical">
    <a-form-item label="Amount">
      <a-input-number
        v-model:value="amount"
        :min="1"
        style="width: 100%"
      />
    </a-form-item>

    <a-form-item label="Notes">
      <a-textarea v-model:value="notes" rows="3" />
    </a-form-item>

    <a-button type="primary" block @click="submit">
      Add Payment
    </a-button>
  </a-form>
</template>

<script setup>
import { ref } from "vue";

const emit = defineEmits(["add"]);

const amount = ref(0);
const notes = ref("");

const submit = () => {
  emit("add", {
    amount: amount.value,
    notes: notes.value,
  });

  amount.value = 0;
  notes.value = "";
};
</script>
