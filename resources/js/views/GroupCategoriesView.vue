<template>
  <v-container class="py-10 catalog-view">
    <div class="text-center mb-10">
      <h1 class="syne-font text-h3 font-weight-bold mb-3">
        {{ groupConfig?.title || 'Kategorija' }}
      </h1>
      <p class="text-subtitle-1 text-medium-emphasis">
        Izvēlieties apakškategoriju, lai redzētu atbilstošās preces
      </p>
    </div>

    <v-row v-if="subcategories.length">
      <v-col
        v-for="sub in subcategories"
        :key="sub.kategorijas_id"
        cols="12"
        sm="6"
        md="4"
        class="mb-6"
      >
        <v-card
          class="subcategory-card"
          elevation="2"
          rounded="lg"
          @click="goToProducts(sub)"
        >
          <v-card-text>
            <div class="d-flex flex-column">
              <span class="text-subtitle-1 font-weight-medium">
                {{ sub.nosaukums }}
              </span>
              <span class="text-body-2 text-medium-emphasis">
                {{ sub.apraksts }}
              </span>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row v-else>
      <v-col cols="12">
        <v-alert type="info" variant="tonal">
          Šai kategorijai nav definētas apakškategorijas. Lūdzu, atgriezieties pie
          <router-link to="/kategorijas" class="font-weight-bold">kataloga</router-link>.
        </v-alert>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()
const categories = ref([])

const GROUPS = [
  {
    slug: 'telefoni',
    title: 'Telefoni',
    subcategoryNames: [
      'Telefoni — ierīces',
      'Telefoni — vāciņi',
      'Telefoni — lādētāji'
    ]
  },
  {
    slug: 'sadzives-tehnika',
    title: 'Sadzīves tehnika',
    subcategoryNames: [
      'Sadzīves tehnika — ledusskapji',
      'Sadzīves tehnika — cepeškrāsnis'
    ]
  }
]

const groupConfig = computed(() =>
  GROUPS.find(g => g.slug === route.params.groupSlug)
)

const subcategories = computed(() => {
  if (!groupConfig.value) return []
  const byName = Object.fromEntries(
    categories.value.map(c => [c.nosaukums, c])
  )
  return groupConfig.value.subcategoryNames
    .map(name => byName[name])
    .filter(Boolean)
})

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

function goToProducts(category) {
  if (!category?.kategorijas_id) return
  router.push({
    name: 'group-products',
    params: { groupSlug: route.params.groupSlug },
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
</style>

