<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { useElementsStore } from '../../stores/elementsStore';
import { useCanvasStore } from '../../stores/canvasStore';
import { extractNodeData, buildStandaloneSvg } from '../../utils/svgExtractor';
import { 
  X, 
  Scissors, 
  Layers, 
  Save, 
  PlusCircle, 
  Check, 
  MousePointer, 
  Tag, 
  FolderPlus,
  Maximize2
} from 'lucide-vue-next';

const elementsStore = useElementsStore();
const canvasStore = useCanvasStore();

const svgContainerRef = ref(null);
const hoveredNodeName = ref('');
const hoveredBBox = ref(null);
const extractedData = ref(null);
const previewSvgHtml = ref('');

// Form state
const componentTitle = ref('');
const componentCategory = ref('Предметы');
const componentTags = ref('');

const isSaving = ref(false);
const saveSuccess = ref(false);

// List of discoverable semantic groups inside the illustration
const detectedGroups = ref([]);

// Categories list
const defaultCategories = [
  'Персонажи',
  'Здания',
  'Мебель',
  'Природа',
  'Транспорт',
  'Предметы',
  'Декор',
];

watch(
  () => elementsStore.selectedIllustration,
  (newVal) => {
    if (newVal) {
      extractedData.value = null;
      previewSvgHtml.value = '';
      saveSuccess.value = false;
      nextTick(() => {
        initSvgInspection();
      });
    }
  },
  { immediate: true }
);

function initSvgInspection() {
  if (!svgContainerRef.value) return;
  const svg = svgContainerRef.value.querySelector('svg');
  if (!svg) return;

  // Make SVG responsive
  svg.setAttribute('width', '100%');
  svg.setAttribute('height', '100%');
  svg.style.maxHeight = '520px';

  // Discover semantic groups with id or data-name
  const groups = svg.querySelectorAll('g[id], g[data-name]');
  const found = [];
  groups.forEach((g) => {
    const id = g.id || '';
    const name = g.getAttribute('data-name') || id;
    const cat = g.getAttribute('data-category') || 'Объекты';
    if (name && id !== 'ground' && id !== 'sky') {
      found.push({
        element: g,
        id,
        name,
        category: cat,
      });
    }
  });

  detectedGroups.value = found;

  // Select the first group automatically for a great initial experience!
  if (found.length > 0) {
    selectTargetElement(found[0].element);
  }
}

function handleSvgMouseMove(e) {
  const target = e.target;
  if (!target || !svgContainerRef.value) return;

  // Find nearest logical group or element
  const group = target.closest('g[id], g[data-name]') || target;
  if (group && group !== svgContainerRef.value.querySelector('svg')) {
    const name = group.getAttribute('data-name') || group.id || target.tagName;
    hoveredNodeName.value = name;
  }
}

function handleSvgMouseLeave() {
  hoveredNodeName.value = '';
}

function handleSvgClick(e) {
  const target = e.target;
  if (!target || !svgContainerRef.value) return;

  const svgRoot = svgContainerRef.value.querySelector('svg');
  if (target === svgRoot) return;

  // Prefer meaningful group over single inner path if available
  const group = target.closest('g[id], g[data-name]') || target;
  selectTargetElement(group);
}

function selectTargetElement(element) {
  if (!element || !svgContainerRef.value) return;
  const svgRoot = svgContainerRef.value.querySelector('svg');
  if (!svgRoot) return;

  // Remove previous active classes
  svgRoot.querySelectorAll('.svg-inspect-active').forEach((el) => {
    el.classList.remove('svg-inspect-active');
  });

  // Highlight current active element
  element.classList.add('svg-inspect-active');

  // Extract element data
  const data = extractNodeData(element, svgRoot);
  if (data) {
    extractedData.value = data;
    componentTitle.value = data.title;
    componentCategory.value = data.category || 'Предметы';
    componentTags.value = '';
    previewSvgHtml.value = buildStandaloneSvg(data.svgContent, data.viewBox, data.defs);
    saveSuccess.value = false;
  }
}

async function handleSaveComponent(andAddToCanvas = false) {
  if (!extractedData.value) return;

  isSaving.value = true;
  try {
    const tagsArray = componentTags.value
      ? componentTags.value.split(',').map((t) => t.trim()).filter(Boolean)
      : [];

    const payload = {
      illustration_id: elementsStore.selectedIllustration?.id || null,
      title: componentTitle.value.trim() || extractedData.value.title,
      category: componentCategory.value,
      svg_content: extractedData.value.svgContent,
      view_box: extractedData.value.viewBox,
      width: extractedData.value.width,
      height: extractedData.value.height,
      tags: tagsArray,
    };

    const savedComp = await elementsStore.saveExtractedComponent(payload);
    saveSuccess.value = true;

    if (andAddToCanvas) {
      canvasStore.addElement(savedComp);
      // Close modal to see the element on the canvas
      elementsStore.isInspectorOpen = false;
    }

    setTimeout(() => {
      saveSuccess.value = false;
    }, 2500);
  } catch (err) {
    alert('Ошибка при сохранении компонента');
  } finally {
    isSaving.value = false;
  }
}

function handleAddDirectlyToCanvas() {
  handleSaveComponent(true);
}
</script>

<template>
  <div 
    v-if="elementsStore.isInspectorOpen && elementsStore.selectedIllustration"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fade-in"
  >
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-6xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-900">
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
            <Scissors class="w-4 h-4" />
          </div>
          <div>
            <div class="flex items-center space-x-2">
              <h2 class="text-base font-bold text-white">
                Инспектор SVG: {{ elementsStore.selectedIllustration.title }}
              </h2>
              <span class="text-xs px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700">
                {{ elementsStore.selectedIllustration.category }}
              </span>
            </div>
            <p class="text-xs text-slate-400">
              Наведите курсор и кликните на любой объект в иллюстрации, чтобы выделить и извлечь его в библиотеку
            </p>
          </div>
        </div>

        <button 
          @click="elementsStore.isInspectorOpen = false"
          class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Main Workspace: Left Inspector + Right Preview/Save Panel -->
      <div class="flex-1 overflow-hidden grid grid-cols-1 lg:grid-cols-12 divide-y lg:divide-y-0 lg:divide-x divide-slate-800">
        
        <!-- Left: Interactive SVG Inspector View (7 cols) -->
        <div class="lg:col-span-7 p-5 flex flex-col bg-slate-950/40 overflow-y-auto">
          <!-- Instruction / Hover Status Bar -->
          <div class="mb-3 px-3.5 py-2 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center justify-between text-xs">
            <div class="flex items-center space-x-2 text-slate-300">
              <MousePointer class="w-3.5 h-3.5 text-blue-400 animate-pulse" />
              <span>
                {{ hoveredNodeName ? `Объект под курсором: ${hoveredNodeName}` : 'Кликните по любому объекту на изображении' }}
              </span>
            </div>
            <span class="text-[11px] text-slate-500 font-mono">
              Интерактивный DOM-режим
            </span>
          </div>

          <!-- SVG Canvas Wrapper -->
          <div 
            ref="svgContainerRef"
            @mousemove="handleSvgMouseMove"
            @mouseleave="handleSvgMouseLeave"
            @click="handleSvgClick"
            class="flex-1 bg-slate-900/80 rounded-xl border border-slate-800 p-4 flex items-center justify-center relative select-none cursor-pointer overflow-hidden min-h-[380px]"
            v-html="elementsStore.selectedIllustration.svg_content"
          ></div>

          <!-- Quick Select Identified Groups Toolbar -->
          <div class="mt-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-semibold text-slate-300 flex items-center space-x-1.5">
                <Layers class="w-3.5 h-3.5 text-blue-400" />
                <span>Обнаруженные логические группы иллюстрации:</span>
              </span>
              <span class="text-[11px] text-slate-400">{{ detectedGroups.length }} групп</span>
            </div>

            <div class="flex flex-wrap gap-2 max-h-28 overflow-y-auto pr-1">
              <button 
                v-for="grp in detectedGroups" 
                :key="grp.id"
                @click="selectTargetElement(grp.element)"
                :class="extractedData && extractedData.title === grp.name 
                  ? 'bg-blue-600 text-white border-blue-500 shadow-md shadow-blue-500/20' 
                  : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border-slate-800'"
                class="text-xs px-2.5 py-1.5 rounded-lg border font-medium transition flex items-center space-x-1.5 active:scale-95"
              >
                <span>{{ grp.name }}</span>
                <span class="text-[10px] opacity-60">({{ grp.category }})</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Right: Extracted Component Preview & Form (5 cols) -->
        <div class="lg:col-span-5 p-5 flex flex-col justify-between bg-slate-900/90 overflow-y-auto">
          <div v-if="extractedData" class="space-y-4">
            <!-- Header of extraction -->
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
              <div>
                <h3 class="text-sm font-bold text-white">Извлечённый векторный объект</h3>
                <p class="text-xs text-slate-400">Сохранение векторной структуры, пропорций и цветов</p>
              </div>
              <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                100% Вектор
              </span>
            </div>

            <!-- Isolated Component SVG Preview -->
            <div class="bg-slate-950/80 rounded-xl border border-slate-800 p-4 flex flex-col items-center justify-center relative min-h-[190px]">
              <div class="w-40 h-40 flex items-center justify-center" v-html="previewSvgHtml"></div>
              
              <!-- Technical Specs Badges -->
              <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between text-[10px] text-slate-400 font-mono px-2">
                <span>Размеры: {{ extractedData.width }} × {{ extractedData.height }} px</span>
                <span>viewBox: {{ extractedData.viewBox }}</span>
              </div>
            </div>

            <!-- Configuration Form -->
            <div class="space-y-3 pt-2">
              <!-- Title -->
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">
                  Название компонента
                </label>
                <input 
                  v-model="componentTitle" 
                  type="text" 
                  placeholder="Например: Велосипедист"
                  class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:border-blue-500 focus:outline-none transition"
                />
              </div>

              <!-- Category -->
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">
                  Категория
                </label>
                <div class="grid grid-cols-2 gap-2">
                  <select 
                    v-model="componentCategory"
                    class="bg-slate-800/80 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:border-blue-500 focus:outline-none transition"
                  >
                    <option v-for="cat in defaultCategories" :key="cat" :value="cat">
                      {{ cat }}
                    </option>
                  </select>
                  <input 
                    v-model="componentCategory" 
                    type="text" 
                    placeholder="Или своя категория"
                    class="bg-slate-800/80 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:border-blue-500 focus:outline-none transition"
                  />
                </div>
              </div>

              <!-- Tags -->
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">
                  Теги (через запятую)
                </label>
                <div class="relative">
                  <Tag class="w-3.5 h-3.5 text-slate-500 absolute left-3 top-2.5" />
                  <input 
                    v-model="componentTags" 
                    type="text" 
                    placeholder="город, персонаж, спорт"
                    class="w-full bg-slate-800/80 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:border-blue-500 focus:outline-none transition"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Empty placeholder if no element selected yet -->
          <div v-else class="flex-1 flex flex-col items-center justify-center text-center p-8 text-slate-500">
            <MousePointer class="w-10 h-10 mb-3 text-slate-600 animate-bounce" />
            <p class="text-sm font-medium text-slate-400">Элемент пока не выбран</p>
            <p class="text-xs text-slate-500 mt-1 max-w-xs">
              Кликните по любому объекту на иллюстрации слева, чтобы изолировать его и настроить параметры
            </p>
          </div>

          <!-- Actions Footer -->
          <div class="pt-4 border-t border-slate-800 space-y-2">
            <!-- Save to Library button -->
            <button 
              @click="handleSaveComponent(false)"
              :disabled="!extractedData || isSaving"
              class="w-full flex items-center justify-center space-x-2 text-xs font-semibold py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-lg shadow-blue-500/20 transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Check v-if="saveSuccess" class="w-4 h-4 text-emerald-300" />
              <FolderPlus v-else class="w-4 h-4" />
              <span>{{ saveSuccess ? 'Сохранено в библиотеку!' : 'Сохранить в библиотеку' }}</span>
            </button>

            <!-- Save & Add to Canvas button -->
            <button 
              @click="handleAddDirectlyToCanvas"
              :disabled="!extractedData || isSaving"
              class="w-full flex items-center justify-center space-x-2 text-xs font-semibold py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <PlusCircle class="w-4 h-4 text-emerald-400" />
              <span>Сохранить и сразу добавить на холст</span>
            </button>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>
