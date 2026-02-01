<template>
  <v-container class="py-8">
    <v-row justify="center">
      <v-col cols="12" sm="8" md="5">
        <v-card elevation="2" rounded="lg">
          <v-card-title class="text-h5 pa-4">Pieslēgties</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="handleLogin" ref="formRef">
              <v-text-field
                v-model="epasts"
                label="E-pasts"
                type="email"
                variant="outlined"
                density="comfortable"
                :error-messages="errors.epasts"
                autocomplete="email"
              ></v-text-field>
              <v-text-field
                v-model="parole"
                label="Parole"
                type="password"
                variant="outlined"
                density="comfortable"
                :error-messages="errors.parole"
                autocomplete="current-password"
              ></v-text-field>
              <v-btn
                type="submit"
                color="primary"
                block
                size="large"
                :loading="authStore.loading"
                class="mt-2"
              >
                Pieslēgties
              </v-btn>
            </v-form>
          </v-card-text>
          <v-card-actions class="px-4 pb-4">
            <v-spacer></v-spacer>
            <router-link to="/register" class="text-primary text-decoration-none">
              Nav konta? Reģistrēties
            </router-link>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useSnackbarStore } from '@/stores/snackbar'

const router = useRouter()
const authStore = useAuthStore()
const snackbarStore = useSnackbarStore()

const epasts = ref('')
const parole = ref('')
const formRef = ref(null)
const errors = reactive({ epasts: [], parole: [] })

const handleLogin = async () => {
  errors.epasts = []
  errors.parole = []
  if (!epasts.value?.trim()) {
    errors.epasts = ['E-pasts ir obligāts']
    return
  }
  if (!parole.value) {
    errors.parole = ['Parole ir obligāta']
    return
  }
  try {
    await authStore.login({ epasts: epasts.value.trim(), parole: parole.value })
    snackbarStore.showSuccess('Pieslēgšanās veiksmīga')
    router.push('/products')
  } catch (err) {
    const msg = err.response?.data?.message || err.response?.data?.errors?.epasts?.[0] || 'Pieslēgšanās neizdevās'
    errors.epasts = [typeof msg === 'string' ? msg : 'Nepareizi dati']
  }
}
</script>
