<script setup>
import { ref } from 'vue';
import { useCanvasStore } from '../stores/canvasStore';
import { useElementsStore } from '../stores/elementsStore';
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
  Sparkles
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
  <header class="h-14 border-b border-slate-800 bg-slate-900/90 backdrop-blur px-4 flex items-center justify-between z-30 select-none">
    <!-- Left: Brand and Title -->
    <div class="flex items-center space-x-3">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
        <Sparkles class="w-5 h-5 text-white" />
      </div>
      <div>
        <div class="flex items-center space-x-2">
          <span class="font-bold text-sm tracking-wide bg-gradient-to-r from-blue-400 to-indigo-300 bg-clip-text text-transparent">
            VectorCraft
          </span>
          <span class="text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20">
            MVP
          </span>
        </div>
        <input 
          v-model="canvasStore.projectTitle" 
          type="text" 
          placeholder="Название композиции"
          class="text-xs text-slate-300 hover:text-white bg-transparent border border-transparent hover:border-slate-700 focus:border-blue-500 focus:bg-slate-800/80 px-1.5 py-0.5 rounded outline-none transition-all w-52"
        />
      </div>
    </div>

    <!-- Center: Toolbar Controls -->
    <div class="flex items-center space-x-1 bg-slate-950/60 p-1 rounded-xl border border-slate-800">
      <!-- Undo / Redo -->
      <button 
        @click="canvasStore.undo()" 
        title="Отменить (Ctrl+Z)"
        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition"
      >
        <RotateCcw class="w-4 h-4" />
      </button>
      <button 
        @click="canvasStore.redo()" 
        title="Повторить (Ctrl+Y)"
        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition"
      >
        <RotateCw class="w-4 h-4" />
      </button>

      <div class="h-4 w-px bg-slate-800 mx-1"></div>

      <!-- Snap to Grid -->
      <button 
        @click="canvasStore.snapToGrid = !canvasStore.snapToGrid" 
        :title="canvasStore.snapToGrid ? 'Привязка к сетке: ВКЛ' : 'Привязка к сетке: ВЫКЛ'"
        :class="canvasStore.snapToGrid ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30' : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800'"
        class="p-1.5 rounded-lg transition"
      >
        <Grid class="w-4 h-4" />
      </button>

      <div class="h-4 w-px bg-slate-800 mx-1"></div>

      <!-- Zoom Controls -->
      <button 
        @click="zoomOut" 
        title="Уменьшить"
        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition"
      >
        <ZoomOut class="w-4 h-4" />
      </button>
      <button 
        @click="resetZoom" 
        title="Сбросить масштаб (100%)"
        class="text-xs font-mono px-2 py-1 text-slate-300 hover:text-white hover:bg-slate-800 rounded-lg transition"
      >
        {{ Math.round(canvasStore.zoom * 100) }}%
      </button>
      <button 
        @click="zoomIn" 
        title="Увеличить"
        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-800 transition"
      >
        <ZoomIn class="w-4 h-4" />
      </button>

      <div class="h-4 w-px bg-slate-800 mx-1"></div>

      <!-- Clear Canvas -->
      <button 
        @click="canvasStore.clearCanvas()" 
        title="Очистить холст"
        class="p-1.5 rounded-lg text-red-400 hover:text-red-300 hover:bg-red-500/10 transition"
      >
        <Trash2 class="w-4 h-4" />
      </button>
    </div>

    <!-- Right: Actions -->
    <div class="flex items-center space-x-2">
      <!-- Open 20 Illustrations catalog button -->
      <button 
        @click="elementsStore.isIllustrationsModalOpen = true"
        class="flex items-center space-x-2 text-xs font-medium px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 hover:border-slate-600 transition"
      >
        <FolderOpen class="w-4 h-4 text-amber-400" />
        <span>Каталог 20 иллюстраций</span>
      </button>

      <!-- Save Project to Backend -->
      <button 
        @click="handleSaveProject"
        :disabled="isSaving"
        class="flex items-center space-x-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 hover:border-slate-600 transition disabled:opacity-50"
      >
        <Check v-if="saveSuccess" class="w-4 h-4 text-emerald-400" />
        <Save v-else class="w-4 h-4 text-blue-400" />
        <span>{{ saveSuccess ? 'Сохранено!' : 'Сохранить' }}</span>
      </button>

      <!-- Export SVG Button (Primary CTA) -->
      <button 
        @click="canvasStore.isExportModalOpen = true"
        class="flex items-center space-x-1.5 text-xs font-semibold px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-lg shadow-blue-500/25 transition active:scale-95"
      >
        <Download class="w-4 h-4" />
        <span>Экспорт SVG</span>
      </button>
    </div>
  </header>
</template>
