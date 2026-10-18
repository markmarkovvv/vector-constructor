<script setup>
import { ref, computed } from 'vue';
import { useElementsStore } from '../../stores/elementsStore';
import { useCanvasStore } from '../../stores/canvasStore';
import { 
  Search, 
  FolderOpen, 
  Trash2, 
  Plus, 
  Sparkles, 
  Boxes,
  Tag
} from 'lucide-vue-next';

const elementsStore = useElementsStore();
const canvasStore = useCanvasStore();

const search = ref('');
const selectedCat = ref('all');

const displayCategories = computed(() => {
  return ['all', ...elementsStore.categories];
});

const filteredList = computed(() => {
  return elementsStore.components.filter((c) => {
    const matchCat = selectedCat.value === 'all' || c.category === selectedCat.value;
    const matchSearch =
      !search.value ||
      c.title.toLowerCase().includes(search.value.toLowerCase()) ||
      c.category.toLowerCase().includes(search.value.toLowerCase());
    return matchCat && matchSearch;
  });
});

function handleAdd(comp) {
  canvasStore.addElement(comp);
}

function handleDragStart(e, comp) {
  e.dataTransfer.setData('application/json', JSON.stringify(comp));
  e.dataTransfer.effectAllowed = 'copy';
}

async function handleDelete(e, id) {
  e.stopPropagation();
  if (confirm('Удалить компонент из библиотеки?')) {
    await elementsStore.deleteComponent(id);
  }
}
</script>

<template>
  <aside class="w-72 border-r border-slate-800 bg-slate-900/90 flex flex-col z-20 select-none">
    <!-- Header with "Extract from 20 SVG" CTA -->
    <div class="p-3.5 border-b border-slate-800 space-y-2.5">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
          <Boxes class="w-4 h-4 text-blue-400" />
          <h2 class="text-xs font-bold text-white tracking-wide uppercase">Библиотека элементов</h2>
        </div>
        <span class="text-[11px] font-semibold text-slate-400 bg-slate-800 px-2 py-0.5 rounded-full">
          {{ elementsStore.components.length }}
        </span>
      </div>

      <!-- Action Button: Open 20 Illustrations -->
      <button 
        @click="elementsStore.isIllustrationsModalOpen = true"
        class="w-full flex items-center justify-center space-x-2 py-2 px-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-semibold shadow-md shadow-blue-500/15 transition active:scale-95"
      >
        <FolderOpen class="w-4 h-4" />
        <span>Каталог 20 иллюстраций</span>
      </button>

      <!-- Search input -->
      <div class="relative">
        <Search class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-2.5" />
        <input 
          v-model="search" 
          type="text" 
          placeholder="Поиск по компонентам..."
          class="w-full bg-slate-800/80 border border-slate-700/60 rounded-xl pl-8 pr-2.5 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:border-blue-500 focus:outline-none transition"
        />
      </div>
    </div>

    <!-- Category Filter Pills -->
    <div class="px-3 py-2 border-b border-slate-800/70 overflow-x-auto flex items-center space-x-1.5 bg-slate-950/20">
      <button 
        v-for="cat in displayCategories" 
        :key="cat"
        @click="selectedCat = cat"
        :class="selectedCat === cat ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
        class="text-[11px] px-2.5 py-1 rounded-lg font-medium transition whitespace-nowrap"
      >
        {{ cat === 'all' ? 'Все' : cat }}
      </button>
    </div>

    <!-- Components Grid List -->
    <div class="flex-1 overflow-y-auto p-3 space-y-2.5">
      <div 
        v-for="comp in filteredList" 
        :key="comp.id"
        draggable="true"
        @dragstart="handleDragStart($event, comp)"
        @click="handleAdd(comp)"
        class="group relative bg-slate-950/60 hover:bg-slate-800/80 border border-slate-800 hover:border-blue-500/50 rounded-xl p-2.5 transition-all duration-150 cursor-pointer flex items-center space-x-3 hover:shadow-lg active:scale-98"
      >
        <!-- Mini SVG Preview Box -->
        <div class="w-14 h-14 rounded-lg bg-slate-900 border border-slate-800/80 p-1 flex items-center justify-center overflow-hidden flex-shrink-0 group-hover:bg-slate-900/90 transition">
          <svg 
            :viewBox="comp.view_box || '0 0 200 200'" 
            class="w-full h-full pointer-events-none"
            v-html="comp.svg_content"
          ></svg>
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-0 pr-6">
          <h4 class="text-xs font-semibold text-slate-200 group-hover:text-blue-300 transition truncate">
            {{ comp.title }}
          </h4>
          <span class="text-[10px] text-slate-400 block mt-0.5">
            {{ comp.category }}
          </span>
          <span class="text-[9px] text-slate-400 font-mono">
            {{ Math.round(comp.width) }}×{{ Math.round(comp.height) }}px
          </span>
        </div>

        <!-- Quick Add Icon on hover -->
        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 flex items-center space-x-1">
          <button 
            @click="handleDelete($event, comp.id)"
            title="Удалить компонент"
            class="p-1 rounded-md text-slate-500 hover:text-red-400 hover:bg-slate-700/60 opacity-0 group-hover:opacity-100 transition"
          >
            <Trash2 class="w-3.5 h-3.5" />
          </button>
          <div class="p-1 rounded-md bg-blue-500/10 text-blue-400 border border-blue-500/20 group-hover:bg-blue-600 group-hover:text-white transition">
            <Plus class="w-3.5 h-3.5" />
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="filteredList.length === 0" class="text-center py-10 px-4 text-slate-500">
        <Sparkles class="w-8 h-8 mx-auto mb-2 opacity-30 text-blue-400" />
        <p class="text-xs font-medium text-slate-400">Компоненты не найдены</p>
        <p class="text-[11px] text-slate-500 mt-1">
          Откройте каталог 20 иллюстраций и извлеките нужные элементы
        </p>
      </div>
    </div>

    <!-- Drag & Drop hint footer -->
    <div class="p-2.5 border-t border-slate-800/80 bg-slate-950/40 text-center text-[10px] text-slate-400">
      💡 Кликните или перетащите элемент на холст
    </div>
  </aside>
</template>
