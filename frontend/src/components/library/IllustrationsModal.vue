<script setup>
import { ref, computed } from 'vue';
import { useElementsStore } from '../../stores/elementsStore';
import { X, Search, Sparkles, Scissors, Eye, CheckCircle2 } from 'lucide-vue-next';

const elementsStore = useElementsStore();
const searchQuery = ref('');
const selectedCategory = ref('all');

const categories = computed(() => {
  const cats = new Set(elementsStore.illustrations.map((i) => i.category));
  return ['all', ...Array.from(cats)];
});

const filteredIllustrations = computed(() => {
  return elementsStore.illustrations.filter((item) => {
    const matchCat = selectedCategory.value === 'all' || item.category === selectedCategory.value;
    const matchSearch =
      !searchQuery.value ||
      item.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.category.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (item.description && item.description.toLowerCase().includes(searchQuery.value.toLowerCase()));
    return matchCat && matchSearch;
  });
});

function handleOpenInspector(id) {
  elementsStore.openIllustrationInspector(id);
}
</script>

<template>
  <div 
    v-if="elementsStore.isIllustrationsModalOpen" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md animate-fade-in"
  >
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-5xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400">
            <Sparkles class="w-4 h-4" />
          </div>
          <div>
            <h2 class="text-base font-bold text-white">Каталог 20 векторных иллюстраций</h2>
            <p class="text-xs text-slate-400">
              Выберите исходную иллюстрацию, чтобы открыть инспектор и извлечь векторные объекты в библиотеку
            </p>
          </div>
        </div>

        <button 
          @click="elementsStore.isIllustrationsModalOpen = false"
          class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Filters & Search Toolbar -->
      <div class="px-6 py-3 border-b border-slate-800/60 bg-slate-900/50 flex flex-wrap items-center justify-between gap-3">
        <!-- Search bar -->
        <div class="relative w-72">
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Поиск по 20 иллюстрациям..."
            class="w-full bg-slate-800/80 text-xs text-slate-200 placeholder-slate-400 pl-9 pr-3 py-2 rounded-xl border border-slate-700/60 focus:border-blue-500 focus:outline-none transition"
          />
        </div>

        <!-- Category pills -->
        <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 max-w-lg">
          <button 
            v-for="cat in categories" 
            :key="cat"
            @click="selectedCategory = cat"
            :class="selectedCategory === cat ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
            class="text-xs px-2.5 py-1 rounded-lg font-medium transition whitespace-nowrap"
          >
            {{ cat === 'all' ? 'Все иллюстрации' : cat }}
          </button>
        </div>
      </div>

      <!-- Illustrations Grid -->
      <div class="p-6 overflow-y-auto flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div 
          v-for="item in filteredIllustrations" 
          :key="item.id"
          class="bg-slate-950/60 border border-slate-800/80 hover:border-blue-500/50 rounded-xl overflow-hidden transition-all duration-200 hover:shadow-xl hover:shadow-blue-500/5 group flex flex-col"
        >
          <!-- SVG Thumbnail Box -->
          <div class="h-40 bg-slate-900/40 relative flex items-center justify-center p-2 border-b border-slate-800/50 group-hover:bg-slate-900/60 transition">
            <div class="w-full h-full flex items-center justify-center pointer-events-none" v-html="item.svg_content"></div>
            
            <div class="absolute top-2 right-2 flex items-center space-x-1">
              <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">
                #{{ item.id }}
              </span>
            </div>

            <!-- Hover overlay button -->
            <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-xs">
              <button 
                @click="handleOpenInspector(item.id)"
                class="flex items-center space-x-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white shadow-lg transition active:scale-95"
              >
                <Scissors class="w-3.5 h-3.5" />
                <span>Извлечь объекты</span>
              </button>
            </div>
          </div>

          <!-- Card Content -->
          <div class="p-3.5 flex-1 flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-medium text-slate-400">{{ item.category }}</span>
                <span class="text-[10px] text-emerald-400 flex items-center space-x-1">
                  <CheckCircle2 class="w-3 h-3" />
                  <span>{{ item.components_count || 0 }} эл.</span>
                </span>
              </div>
              <h3 class="text-sm font-semibold text-slate-200 group-hover:text-blue-400 transition mb-1">
                {{ item.title }}
              </h3>
              <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                {{ item.description }}
              </p>
            </div>

            <div class="mt-3 pt-2.5 border-t border-slate-800/60 flex items-center justify-between">
              <button 
                @click="handleOpenInspector(item.id)"
                class="w-full text-xs font-medium py-1.5 rounded-lg bg-slate-800 hover:bg-blue-600 hover:text-white text-slate-300 transition flex items-center justify-center space-x-1.5"
              >
                <Scissors class="w-3.5 h-3.5" />
                <span>Открыть инспектор</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Info -->
      <div class="px-6 py-3 border-t border-slate-800 bg-slate-950/40 flex items-center justify-between text-xs text-slate-400">
        <span>Всего в каталоге: {{ elementsStore.illustrations.length }} иллюстраций в едином стиле</span>
        <span>Тестовое задание: MVP Конструктор векторных композиций</span>
      </div>
    </div>
  </div>
</template>
