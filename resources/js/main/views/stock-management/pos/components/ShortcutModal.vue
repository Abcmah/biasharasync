<template>
  <a-modal
    :open="helpVisible"
    title="Keyboard Shortcuts"
    :footer="null"
    width="600px"
    @cancel="handleClose"
  >
    <div class="shortcut-grid">
      <div v-for="item in shortcuts" :key="item.key" class="shortcut-row">
        <span class="shortcut-desc">{{ item.desc }}</span>
        <div class="shortcut-keys">
          <kbd v-for="k in item.keys" :key="k">{{ k }}</kbd>
        </div>
      </div>
    </div>
  </a-modal>
</template>
<script setup>
const props = defineProps({
    helpVisible: Boolean,
});
const emit = defineEmits(["handleClose"]);
const shortcuts = [
    { desc: 'Complete Sale / Checkout', keys: ['F10', 'Numpad +'] },
    { desc: 'Trigger M-PESA Push', keys: ['F5'] },
    { desc: 'Create Quotation', keys: ['F7'] },
    { desc: 'Toggle Fullscreen', keys: 'Double Space' },
    { desc: 'Suspend Sale', keys: ['F8'] },
    { desc: 'Restore Sales', keys: ['F9'] },
    { desc: 'Search Products', keys: ['F1'] },
    { desc: 'Show this Help', keys: ['?'] },
];
const handleClose = () => {
    emit("closeHelpModal");
};
</script>
<style scoped>
.shortcut-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 10px 0;
}
.shortcut-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 12px;
  background: #f9fafb;
  border-radius: 8px;
}
.shortcut-desc {
  font-weight: 500;
  color: #4b5563;
}
kbd {
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  box-shadow: 0 2px 0 #d1d5db;
  color: #111827;
  display: inline-block;
  font-size: 12px;
  font-weight: 700;
  padding: 2px 6px;
  margin-left: 4px;
}
</style>


