<script setup>
import { ref, computed } from 'vue';
import { useElementsStore } from '../../stores/elementsStore';
import { X, Search, Sparkles, Scissors, Eye, CheckCircle2, ArrowRight } from 'lucide-vue-next';

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
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/85 backdrop-blur-md animate-fade-in"
  >
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-6xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/80">
        <div class="flex items-center space-x-3">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500/20 to-orange-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 shadow-sm">
            <Sparkles class="w-5 h-5" />
          </div>
          <div>
            <h2 class="text-base font-bold text-white tracking-wide">
              Каталог 20 векторных иллюстраций
            </h2>
            <p class="text-xs text-slate-400">
              Выберите любую иллюстрацию, чтобы открыть интерактивный инспектор и извлечь элементы в библиотеку
            </p>
          </div>
        </div>

        <button 
          @click="elementsStore.isIllustrationsModalOpen = false"
          class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Filters & Search Toolbar -->
      <div class="px-6 py-3 border-b border-slate-800/80 bg-slate-950/40 flex flex-wrap items-center justify-between gap-3">
        <!-- Search bar -->
        <div class="relative w-80">
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Поиск по названию или описанию..."
            class="w-full bg-slate-800/80 text-xs text-slate-100 placeholder-slate-400 pl-9 pr-3 py-2 rounded-xl border border-slate-700/60 focus:border-blue-500 focus:outline-none transition"
          />
        </div>

        <!-- Category pills (with smooth scroll and no truncation) -->
        <div class="flex items-center space-x-2 overflow-x-auto pb-1 max-w-full">
          <button 
            v-for="cat in categories" 
            :key="cat"
            @click="selectedCategory = cat"
            :class="selectedCategory === cat ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25' : 'bg-slate-800/90 text-slate-300 hover:bg-slate-700 border border-slate-700/50'"
            class="text-xs px-3 py-1.5 rounded-xl font-medium transition whitespace-nowrap active:scale-95 cursor-pointer"
          >
            {{ cat === 'all' ? 'Все иллюстрации (20)' : cat }}
          </button>
        </div>
      </div>

      <!-- Illustrations Grid (responsive: 1 col on mobile, 2 on sm, 3 on lg, 4 on xl) -->
      <div class="p-6 overflow-y-auto flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        <div 
          v-for="item in filteredIllustrations" 
          :key="item.id"
          @click="handleOpenInspector(item.id)"
          class="bg-slate-950/70 border border-slate-800/90 hover:border-blue-500/60 rounded-2xl overflow-hidden transition-all duration-200 hover:shadow-2xl hover:shadow-blue-500/10 group flex flex-col cursor-pointer"
        >
          <!-- SVG Thumbnail Box (high contrast backdrop so vector colors stand out) -->
          <div class="h-44 bg-slate-900/60 relative flex items-center justify-center p-3 border-b border-slate-800/80 group-hover:bg-slate-900/90 transition overflow-hidden">
            <!-- Render live SVG -->
            <div 
              class="w-full h-full flex items-center justify-center pointer-events-none svg-thumb-container"
              v-html="item.svg_content"
            ></div>
            
            <!-- Badges overlay -->
            <div class="absolute top-2.5 left-2.5">
              <span class="text-[11px] font-bold font-mono px-2 py-0.5 rounded-lg bg-slate-950/80 text-blue-400 border border-slate-800 backdrop-blur-xs">
                #{{ item.id }}
              </span>
            </div>

            <div class="absolute top-2.5 right-2.5 flex items-center space-x-1.5">
              <span class="text-[10px] font-semibold px-2 py-0.5 rounded-lg bg-slate-950/80 text-emerald-400 border border-slate-800 backdrop-blur-xs flex items-center space-x-1">
                <CheckCircle2 class="w-3 h-3 text-emerald-400" />
                <span>{{ item.components_count || 0 }} готово</span>
              </span>
            </div>

            <!-- Hover overlay button -->
            <div class="absolute inset-0 bg-slate-950/70 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-xs">
              <span class="flex items-center space-x-1.5 text-xs font-semibold px-3.5 py-2 rounded-xl bg-blue-600 text-white shadow-xl shadow-blue-600/30">
                <Scissors class="w-4 h-4" />
                <span>Открыть инспектор</span>
              </span>
            </div>
          </div>

          <!-- Card Content -->
          <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
            <div>
              <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-1">
                {{ item.category }}
              </span>
              <h3 class="text-sm font-bold text-slate-100 group-hover:text-blue-400 transition leading-snug">
                {{ item.title }}
              </h3>
              <p class="text-xs text-slate-400 line-clamp-2 mt-1 leading-relaxed">
                {{ item.description }}
              </p>
            </div>

            <div class="pt-2 border-t border-slate-800/70 flex items-center justify-between">
              <span class="text-xs font-medium text-blue-400 group-hover:text-blue-300 flex items-center space-x-1">
                <span>Инспектор объектов</span>
                <ArrowRight class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
              </span>
              <span class="text-[10px] text-slate-500 font-mono">SVG 800×600</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Info -->
      <div class="px-6 py-3.5 border-t border-slate-800 bg-slate-950/60 flex items-center justify-between text-xs text-slate-400">
        <span class="flex items-center space-x-1.5">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>Всего в каталоге: <strong class="text-slate-200">{{ elementsStore.illustrations.length }}</strong> векторных иллюстраций</span>
        </span>
        <span class="text-slate-400">Кликните на карточку для интерактивного извлечения любого объекта</span>
      </div>
    </div>
  </div>
</template>
