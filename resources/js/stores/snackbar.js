import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSnackbarStore = defineStore('snackbar', () => {
  const snackbar = ref({
    show: false,
    message: '',
    color: 'success'
  })

  function showSuccess(message) {
    snackbar.value = { show: true, message, color: 'success' }
  }

  function showError(message) {
    snackbar.value = { show: true, message, color: 'error' }
  }

  function showInfo(message) {
    snackbar.value = { show: true, message, color: 'info' }
  }

  function showWarning(message) {
    snackbar.value = { show: true, message, color: 'warning' }
  }

  return {
    snackbar,
    showSuccess,
    showError,
    showInfo,
    showWarning
  }
})
