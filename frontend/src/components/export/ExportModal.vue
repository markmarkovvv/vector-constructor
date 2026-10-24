<script setup>
import { ref, computed } from 'vue';
import { useCanvasStore } from '../../stores/canvasStore';
import { compileToSvg, downloadSvgFile } from '../../utils/svgCompiler';
import confetti from 'canvas-confetti';
import { 
  X, 
  Download, 
  Copy, 
  Check, 
  Code, 
  Eye, 
  Sparkles, 
  FileCheck 
} from 'lucide-vue-next';

const canvasStore = useCanvasStore();

const includeBackground = ref(true);
const filename = ref('my-vector-scene.svg');
const isCopied = ref(false);
const activeTab = ref('preview'); // 'preview' | 'code'

const compiledSvg = computed(() => {
  return compileToSvg(canvasStore.elements, {
    width: canvasStore.canvasWidth,
    height: canvasStore.canvasHeight,
    backgroundColor: canvasStore.backgroundColor,
    schemeId: canvasStore.currentSchemeId,
    includeBackground: includeBackground.value,
    title: canvasStore.projectTitle,
  });
});

function handleDownload() {
  const name = filename.value.endsWith('.svg') ? filename.value : `${filename.value}.svg`;
  downloadSvgFile(compiledSvg.value, name);

  // Confetti celebration!
  confetti({
    particleCount: 100,
    spread: 70,
    origin: { y: 0.6 },
  });
}

async function handleCopy() {
  try {
    await navigator.clipboard.writeText(compiledSvg.value);
    isCopied.value = true;
    setTimeout(() => {
      isCopied.value = false;
    }, 2000);
  } catch (err) {
    alert('Не удалось скопировать код в буфер обмена');
  }
}
</script>

<template>
  <div 
    v-if="canvasStore.isExportModalOpen"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fade-in"
  >
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
            <Download class="w-4 h-4" />
          </div>
          <div>
            <h2 class="text-base font-bold text-white">Экспорт векторной композиции (SVG)</h2>
            <p class="text-xs text-slate-400">
              Итоговый файл сохраняет 100% векторность путей, цвета, слои и масштабируемость без растеризации
            </p>
          </div>
        </div>

        <button 
          @click="canvasStore.isExportModalOpen = false"
          class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Settings Bar -->
      <div class="px-6 py-3 border-b border-slate-800 bg-slate-950/30 flex flex-wrap items-center justify-between gap-4">
        <!-- Filename & Background Checkbox -->
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-2">
            <span class="text-xs text-slate-400 font-medium">Имя файла:</span>
            <input 
              v-model="filename" 
              type="text" 
              class="bg-slate-800 border border-slate-700 rounded-lg px-2.5 py-1 text-xs text-slate-200 focus:border-blue-500 focus:outline-none w-48"
            />
          </div>

          <label class="flex items-center space-x-2 cursor-pointer text-xs text-slate-300">
            <input 
              v-model="includeBackground" 
              type="checkbox" 
              class="rounded bg-slate-800 border-slate-700 text-blue-600 focus:ring-0"
            />
            <span>Включать цветной фон холста</span>
          </label>
        </div>

        <!-- Preview / Code Tabs -->
        <div class="flex items-center space-x-1 bg-slate-900 border border-slate-800 p-0.5 rounded-lg text-xs">
          <button 
            @click="activeTab = 'preview'"
            :class="activeTab === 'preview' ? 'bg-blue-600 text-white font-medium' : 'text-slate-400 hover:text-slate-200'"
            class="px-2.5 py-1 rounded-md transition flex items-center space-x-1.5"
          >
            <Eye class="w-3.5 h-3.5" />
            <span>Превью</span>
          </button>
          <button 
            @click="activeTab = 'code'"
            :class="activeTab === 'code' ? 'bg-blue-600 text-white font-medium' : 'text-slate-400 hover:text-slate-200'"
            class="px-2.5 py-1 rounded-md transition flex items-center space-x-1.5"
          >
            <Code class="w-3.5 h-3.5" />
            <span>SVG Код</span>
          </button>
        </div>
      </div>

      <!-- Main Body: Preview or Code -->
      <div class="flex-1 p-6 overflow-y-auto bg-slate-950/40 flex items-center justify-center min-h-[380px]">
        <!-- Vector SVG Live Preview -->
        <div 
          v-if="activeTab === 'preview'"
          class="w-full max-h-[460px] flex items-center justify-center rounded-xl bg-slate-900/60 border border-slate-800 p-4 overflow-hidden shadow-inner"
        >
          <div 
            class="max-w-full max-h-full flex items-center justify-center overflow-visible shadow-xl rounded-lg"
            v-html="compiledSvg"
          ></div>
        </div>

        <!-- Raw SVG Source Code View -->
        <div v-else class="w-full h-full max-h-[460px] relative">
          <textarea 
            readonly
            :value="compiledSvg"
            class="w-full h-[440px] bg-slate-900 border border-slate-800 rounded-xl p-4 font-mono text-xs text-emerald-400 leading-relaxed outline-none resize-none selection:bg-blue-500/30"
          ></textarea>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-4 border-t border-slate-800 bg-slate-900 flex items-center justify-between">
        <div class="flex items-center space-x-4 text-xs text-slate-400 font-mono">
          <span>Слоёв: {{ canvasStore.elements.length }}</span>
          <span>Разрешение: {{ canvasStore.canvasWidth }}×{{ canvasStore.canvasHeight }} px</span>
          <span class="text-emerald-400 flex items-center space-x-1">
            <FileCheck class="w-3.5 h-3.5" />
            <span>Чистый векторный XML</span>
          </span>
        </div>

        <div class="flex items-center space-x-2.5">
          <button 
            @click="handleCopy"
            class="flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition active:scale-95"
          >
            <Check v-if="isCopied" class="w-4 h-4 text-emerald-400" />
            <Copy v-else class="w-4 h-4" />
            <span>{{ isCopied ? 'Скопировано!' : 'Копировать код' }}</span>
          </button>

          <button 
            @click="handleDownload"
            class="flex items-center space-x-1.5 px-5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-semibold shadow-lg shadow-blue-500/20 transition active:scale-95"
          >
            <Download class="w-4 h-4" />
            <span>Скачать .SVG файл</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
