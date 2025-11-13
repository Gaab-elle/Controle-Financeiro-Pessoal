<template>
  <transition-group
    name="toast"
    tag="div"
    class="toast-container"
  >
    <div
      v-for="toast in toastStore.toasts"
      :key="toast.id"
      :class="[
        'toast',
        `toast-${toast.type}`
      ]"
      @click="toastStore.remove(toast.id)"
    >
      <div class="toast-content">
        <span class="toast-icon">
          <i v-if="toast.type === 'success'" class="fas fa-check-circle"></i>
          <i v-else-if="toast.type === 'error'" class="fas fa-exclamation-circle"></i>
        </span>
        <span class="toast-message">{{ toast.message }}</span>
        <button
          @click.stop="toastStore.remove(toast.id)"
          class="toast-close"
        >
          ×
        </button>
      </div>
    </div>
  </transition-group>
</template>

<script setup>
import { useToastStore } from '../stores/toast'

const toastStore = useToastStore()
</script>

<style scoped>
.toast-container {
  position: fixed;
  top: 70px;
  right: 20px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 400px;
  pointer-events: none;
}

.toast {
  pointer-events: auto;
  padding: 16px 20px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  cursor: pointer;
  transition: all 0.3s ease;
  animation: slideIn 0.3s ease;
}

.toast:hover {
  transform: translateX(-5px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.toast-success {
  background-color: #10B981;
  color: white;
  border-left: 4px solid #059669;
}

.toast-error {
  background-color: #EF4444;
  color: white;
  border-left: 4px solid #DC2626;
}

.toast-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.toast-icon {
  font-size: 20px;
  flex-shrink: 0;
}

.toast-message {
  flex: 1;
  font-size: 14px;
  font-weight: 500;
  line-height: 1.5;
}

.toast-close {
  background: none;
  border: none;
  color: white;
  font-size: 24px;
  line-height: 1;
  cursor: pointer;
  opacity: 0.8;
  transition: opacity 0.2s;
  padding: 0;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.toast-close:hover {
  opacity: 1;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-leave-to {
  transform: translateX(100%);
  opacity: 0;
}

/* Responsividade */
@media (max-width: 640px) {
  .toast-container {
    top: 70px;
    right: 10px;
    left: 10px;
    max-width: none;
  }

  .toast {
    padding: 14px 16px;
  }

  .toast-message {
    font-size: 13px;
  }
}
</style>

