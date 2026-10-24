<script setup>
import { ref } from 'vue';
import { useCanvasStore } from '../stores/canvasStore';
import { useElementsStore } from '../stores/elementsStore';
import { CANVAS_FORMATS } from '../utils/canvasFormats';
import { COLOR_SCHEMES } from '../utils/colorSchemes';
import { 
  FolderOpen, 
  Download, 
  RotateCcw, 
  RotateCw, 
  Grid, 
  ZoomIn, 
  ZoomOut, 
  Trash2, 
  Save, 
  Check, 
  Layers,
  Sparkles,
  Square,
  RectangleHorizontal,
  RectangleVertical,
  Palette,
  Wand2
} from 'lucide-vue-next';

const canvasStore = useCanvasStore();
const elementsStore = useElementsStore();

const isSaving = ref(false);
const saveSuccess = ref(false);

async function handleSaveProject() {
  isSaving.value = true;
  try {
    await canvasStore.saveToBackend();
    saveSuccess.value = true;
    setTimeout(() => {
      saveSuccess.value = false;
    }, 2500);
  } catch (err) {
    alert('Не удалось сохранить проект на сервере');
  } finally {
    isSaving.value = false;
  }
}

function zoomIn() {
  canvasStore.zoom = Math.min(2, +(canvasStore.zoom + 0.1).toFixed(1));
}

function zoomOut() {
  canvasStore.zoom = Math.max(0.4, +(canvasStore.zoom - 0.1).toFixed(1));
}

function resetZoom() {
  canvasStore.zoom = 1;
}
</script>

<template>
  <header class="h-14 border-b border-slate-800 bg-slate-900/90 backdrop-blur px-3 sm:px-4 flex items-center justify-between z-30 select-none">
    <!-- Left: Brand, Title & Auto-Composer CTA -->
    <div class="flex items-center space-x-3">
      <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-white">
          <rect x="3" y="3" width="18" height="18" rx="4" />
          <circle cx="8.5" cy="8.5" r="1.5" />
          <polyline points="21 15 16 10 5 21" />
        </svg>
      </div>

      <div class="hidden md:flex flex-col">
        <span class="font-bold text-xs tracking-wider bg-gradient-to-r from-blue-400 to-indigo-300 bg-clip-text text-transparent uppercase">
          VectorCraft
        </span>
        <input 
          v-model="canvasStore.projectTitle" 
          type="text" 
          placeholder="Название композиции"
          class="text-xs text-slate-300 hover:text-white bg-transparent border border-transparent hover:border-slate-700 focus:border-blue-500 focus:bg-slate-800/80 px-1 py-0.5 rounded outline-none transition-all w-36 lg:w-44"
        />
      </div>

      <!-- Smart Composer Trigger (Mandatory Scenario 1 & 2) -->
      <button 
        @click="canvasStore.isSmartComposerOpen = true"
        class="flex items-center space-x-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl bg-gradient-to-r from-violet-600 via-purple-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 text-white shadow-lg shadow-purple-500/20 transition active:scale-95 cursor-pointer"
        title="Собрать композицию по метафоре или списку компонентов"
      >
        <Wand2 class="w-3.5 h-3.5 animate-pulse text-amber-300" />
        <span>Автосборка</span>
      </button>
    </div>

    <!-- Center: Format Presets, Color Schemes & Detail Level -->
    <div class="flex items-center space-x-2">
      <!-- 1. Format Presets (1:1, 16:9, 9:16) -->
      <div class="flex items-center bg-slate-950/70 p-0.5 rounded-xl border border-slate-800">
        <button 
          v-for="fmt in CANVAS_FORMATS" 
          :key="fmt.id"
          @click="canvasStore.setFormat(fmt.id)"
          :title="fmt.description"
          :class="canvasStore.currentFormatId === fmt.id 
            ? 'bg-blue-600 text-white font-bold shadow-sm' 
            : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/70'"
          class="px-2 py-1 rounded-lg text-[11px] font-mono flex items-center space-x-1 transition cursor-pointer"
        >
          <Square v-if="fmt.id === 'square'" class="w-3 h-3" />
          <RectangleHorizontal v-else-if="fmt.id === 'horizontal'" class="w-3 h-3" />
          <RectangleVertical v-else class="w-3 h-3" />
          <span class="hidden sm:inline">{{ fmt.ratio }}</span>
        </button>
      </div>

      <!-- 2. Official 3 Color Schemes -->
      <div class="flex items-center bg-slate-950/70 p-0.5 rounded-xl border border-slate-800 space-x-0.5">
        <button 
          v-for="scheme in Object.values(COLOR_SCHEMES)"
          :key="scheme.id"
          @click="canvasStore.setColorScheme(scheme.id)"
          :title="`Цветовая схема: ${scheme.name} (${scheme.label})`"
          :class="canvasStore.currentSchemeId === scheme.id 
            ? 'bg-slate-800 text-white font-semibold ring-1 ring-blue-500' 
            : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50'"
          class="px-2 py-1 rounded-lg text-[11px] flex items-center space-x-1.5 transition cursor-pointer"
        >
          <span 
            class="w-2.5 h-2.5 rounded-full border border-black/20 shadow-sm shrink-0" 
            :style="{ background: scheme.previewBadge }"
          ></span>
          <span class="hidden lg:inline">{{ scheme.name }}</span>
        </button>
      </div>

      <!-- 3. Detail Level Switcher (L / S) -->
      <div class="flex items-center bg-slate-950/70 p-0.5 rounded-xl border border-slate-800">
        <button 
          @click="canvasStore.setDetailLevel('L')"
          title="Размер L: высокая детализация для больших экранов"
          :class="canvasStore.currentDetailLevel === 'L' 
            ? 'bg-indigo-600 text-white font-bold' 
            : 'text-slate-400 hover:text-white'"
          class="px-2 py-1 rounded-lg text-[11px] font-mono transition cursor-pointer"
        >
          L
        </button>
        <button 
          @click="canvasStore.setDetailLevel('S')"
          title="Размер S: упрощенная детализация для компактных форматов"
          :class="canvasStore.currentDetailLevel === 'S' 
            ? 'bg-indigo-600 text-white font-bold' 
            : 'text-slate-400 hover:text-white'"
          class="px-2 py-1 rounded-lg text-[11px] font-mono transition cursor-pointer"
        >
          S
        </button>
      </div>

      <!-- Canvas Undo / Redo & Zoom -->
      <div class="hidden xl:flex items-center space-x-1 bg-slate-950/60 p-0.5 rounded-xl border border-slate-800">
        <button 
          @click="canvasStore.undo()" 
          title="Отменить (Ctrl+Z)"
          class="p-1 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition"
        >
          <RotateCcw class="w-3.5 h-3.5" />
        </button>
        <button 
          @click="canvasStore.redo()" 
          title="Повторить (Ctrl+Y)"
          class="p-1 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition"
        >
          <RotateCw class="w-3.5 h-3.5" />
        </button>
        <div class="h-3 w-px bg-slate-800"></div>
        <button 
          @click="canvasStore.snapToGrid = !canvasStore.snapToGrid" 
          :title="canvasStore.snapToGrid ? 'Привязка к сетке: ВКЛ' : 'Привязка к сетке: ВЫКЛ'"
          :class="canvasStore.snapToGrid ? 'bg-blue-600/20 text-blue-400' : 'text-slate-400 hover:text-slate-100'"
          class="p-1 rounded-lg transition"
        >
          <Grid class="w-3.5 h-3.5" />
        </button>
        <div class="h-3 w-px bg-slate-800"></div>
        <button 
          @click="zoomOut" 
          title="Уменьшить"
          class="p-1 rounded-lg text-slate-400 hover:text-slate-100 transition"
        >
          <ZoomOut class="w-3.5 h-3.5" />
        </button>
        <button 
          @click="resetZoom" 
          title="Сбросить масштаб"
          class="text-[10px] font-mono px-1.5 text-slate-300 hover:text-white"
        >
          {{ Math.round(canvasStore.zoom * 100) }}%
        </button>
        <button 
          @click="zoomIn" 
          title="Увеличить"
          class="p-1 rounded-lg text-slate-400 hover:text-slate-100 transition"
        >
          <ZoomIn class="w-3.5 h-3.5" />
        </button>
        <div class="h-3 w-px bg-slate-800"></div>
        <button 
          @click="canvasStore.clearCanvas()" 
          title="Очистить холст"
          class="p-1 rounded-lg text-red-400 hover:text-red-300 hover:bg-red-500/10 transition"
        >
          <Trash2 class="w-3.5 h-3.5" />
        </button>
      </div>
    </div>

    <!-- Right: Actions (Catalog, Save, Export) -->
    <div class="flex items-center space-x-2">
      <!-- 20 Illustrations catalog button -->
      <button 
        @click="elementsStore.isIllustrationsModalOpen = true"
        class="hidden sm:flex items-center space-x-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition cursor-pointer"
      >
        <FolderOpen class="w-3.5 h-3.5 text-amber-400" />
        <span>Каталог 20</span>
      </button>

      <!-- Save Project to Backend -->
      <button 
        @click="handleSaveProject"
        :disabled="isSaving"
        class="flex items-center space-x-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition disabled:opacity-50 cursor-pointer"
      >
        <Check v-if="saveSuccess" class="w-3.5 h-3.5 text-emerald-400" />
        <Save v-else class="w-3.5 h-3.5 text-blue-400" />
        <span class="hidden md:inline">{{ saveSuccess ? 'Сохранено!' : 'Сохранить' }}</span>
      </button>

      <!-- Export SVG Button (Primary CTA) -->
      <button 
        @click="canvasStore.isExportModalOpen = true"
        class="flex items-center space-x-1.5 text-xs font-semibold px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-lg shadow-blue-500/25 transition active:scale-95 cursor-pointer"
      >
        <Download class="w-3.5 h-3.5" />
        <span>Экспорт SVG</span>
      </button>
    </div>
  </header>
</template>
