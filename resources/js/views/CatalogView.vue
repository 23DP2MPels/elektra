<template>
  <v-container class="py-10 catalog-view">
    <div class="text-center mb-10">
      <h1 class="syne-font text-h3 font-weight-bold mb-3">ELEKTRA katalogs</h1>
      <p class="text-subtitle-1 text-medium-emphasis">
        Izvēlieties preču kategoriju, lai pārlūkotu tās apakškategorijas un preces
      </p>
    </div>

    <v-row>
      <v-col
        v-for="group in groups"
        :key="group.slug"
        cols="12"
        md="4"
        class="mb-6"
      >
        <v-card
          elevation="2"
          rounded="lg"
          class="group-card"
          @click="goToGroup(group)"
        >
          <v-card-text>
            <div class="d-flex align-center mb-3">
              <v-icon class="mr-3" color="primary" size="32">{{ group.icon }}</v-icon>
              <span class="syne-font text-h5">{{ group.title }}</span>
            </div>
            <p class="text-body-2 text-medium-emphasis mb-0">
              {{ group.description }}
            </p>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row v-if="otherCategories.length" class="mt-4">
      <v-col cols="12">
        <v-card elevation="1" rounded="lg">
          <v-card-title class="d-flex align-center">
            <v-icon class="mr-2" color="primary">mdi-dots-grid</v-icon>
            <span class="syne-font text-h5">Citas kategorijas (piem., televizori, austiņas, cits)</span>
          </v-card-title>
          <v-card-text>
            <v-chip-group column>
              <v-chip
                v-for="cat in otherCategories"
                :key="cat.kategorijas_id"
                class="ma-1"
                color="primary"
                variant="tonal"
                @click="goToCategory(cat)"
              >
                {{ cat.nosaukums }}
              </v-chip>
            </v-chip-group>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const categories = ref([])

const groups = [
  {
    slug: 'telefoni',
    title: 'Telefoni',
    icon: 'mdi-cellphone',
    description: 'Viedtālruņi un to aksesuāri (vāciņi, lādētāji u.c.).'
  },
  {
    slug: 'sadzives-tehnika',
    title: 'Sadzīves tehnika',
    icon: 'mdi-fridge',
    description: 'Ledusskapji, cepeškrāsnis un cita sadzīves tehnika.'
  }
]

const TELEFONI_KEYS = [
  'Telefoni — ierīces',
  'Telefoni — vāciņi',
  'Telefoni — lādētāji'
]

const SADZIVES_KEYS = [
  'Sadzīves tehnika — ledusskapji',
  'Sadzīves tehnika — cepeškrāsnis'
]

const usedCategoryNames = computed(() => [
  ...TELEFONI_KEYS,
  ...SADZIVES_KEYS
])

const otherCategories = computed(() =>
  categories.value.filter(
    c => !usedCategoryNames.value.includes(c.nosaukums)
  )
)

async function loadCategories() {
  try {
    const res = await api.get('/categories')
    categories.value = Array.isArray(res.data)
      ? res.data
      : res.data.data ?? []
  } catch (e) {
    categories.value = []
  }
}

function goToGroup(group) {
  if (!group?.slug) return
  router.push({
    name: 'group-categories',
    params: { groupSlug: group.slug }
  })
}

function goToCategory(category) {
  if (!category?.kategorijas_id) return
  router.push({
    name: 'products',
    query: { category: category.kategorijas_id }
  })
}

onMounted(() => {
  loadCategories()
})
</script>

<style scoped>
.catalog-view {
  background: linear-gradient(to bottom, #f5f7fa 0%, #e4ebf5 100%);
  min-height: 100vh;
}
.subcategory-card {
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.subcategory-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.group-card {
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.group-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}
</style>

