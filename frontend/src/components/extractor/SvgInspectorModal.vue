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
  Maximize2,
  Zap,
  CheckCircle2,
  ChevronLeft,
  ChevronRight,
  ExternalLink,
  Sparkles,
  Loader2
} from 'lucide-vue-next';

const elementsStore = useElementsStore();
const canvasStore = useCanvasStore();

const svgContainerRef = ref(null);
const hoveredNodeName = ref('');
const extractedData = ref(null);
const previewSvgHtml = ref('');

// Form state
const componentTitle = ref('');
const componentCategory = ref('Предметы');
const componentTags = ref('');

const isSaving = ref(false);
const saveSuccess = ref(false);
const canvasAddedToast = ref(false);

// Batch processing state
const isBatchProcessing = ref(false);
const batchProgressMessage = ref('');
const batchSuccessToast = ref('');
const selectedGroupIds = ref(new Set());

// List of discoverable semantic groups inside the illustration
const detectedGroups = ref([]);

// Default categories
const defaultCategories = [
  'Персонажи',
  'Здания',
  'Мебель',
  'Природа',
  'Транспорт',
  'Предметы',
  'Декор',
];

// Navigation between illustrations
const currentIllustrationIndex = computed(() => {
  if (!elementsStore.selectedIllustration) return -1;
  return elementsStore.illustrations.findIndex((i) => i.id === elementsStore.selectedIllustration.id);
});

const hasPrev = computed(() => currentIllustrationIndex.value > 0);
const hasNext = computed(() => currentIllustrationIndex.value !== -1 && currentIllustrationIndex.value < elementsStore.illustrations.length - 1);

function goToPrevIllustration() {
  if (hasPrev.value) {
    const prevItem = elementsStore.illustrations[currentIllustrationIndex.value - 1];
    elementsStore.openIllustrationInspector(prevItem.id);
  }
}

function goToNextIllustration() {
  if (hasNext.value) {
    const nextItem = elementsStore.illustrations[currentIllustrationIndex.value + 1];
    elementsStore.openIllustrationInspector(nextItem.id);
  }
}

watch(
  () => elementsStore.selectedIllustration,
  (newVal) => {
    if (newVal) {
      extractedData.value = null;
      previewSvgHtml.value = '';
      saveSuccess.value = false;
      canvasAddedToast.value = false;
      batchSuccessToast.value = '';
      selectedGroupIds.value = new Set();
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

  svg.setAttribute('width', '100%');
  svg.setAttribute('height', '100%');
  svg.style.maxHeight = '520px';

  // Discover semantic groups with id or data-name, or top graphical elements for custom SVGs
  let elementsToDetect = Array.from(svg.querySelectorAll('g[id], g[data-name]'));
  
  if (elementsToDetect.length === 0) {
    const rawElements = Array.from(svg.children).filter((el) => {
      const tag = el.tagName.toLowerCase();
      return !['defs', 'style', 'title', 'desc', 'metadata'].includes(tag);
    });
    if (rawElements.length > 0) {
      elementsToDetect = rawElements;
    } else {
      elementsToDetect = Array.from(svg.querySelectorAll('path, rect, circle, polygon, g')).slice(0, 30);
    }
  }

  const found = [];
  const groupIds = new Set();

  elementsToDetect.forEach((el, index) => {
    const id = el.id || `elem_${index}`;
    const name = el.getAttribute('data-name') || (el.id ? formatIdToTitle(el.id) : `Элемент #${index + 1} (${el.tagName})`);
    const cat = el.getAttribute('data-category') || 'Объекты';
    if (name && el.id !== 'ground' && el.id !== 'sky') {
      found.push({
        element: el,
        id,
        name,
        category: cat,
      });
      groupIds.add(id);
    }
  });

  detectedGroups.value = found;
  selectedGroupIds.value = new Set(groupIds);

  // Select the first group automatically
  if (found.length > 0) {
    selectTargetElement(found[0].element);
  }
}

function formatIdToTitle(id) {
  return id
    .replace(/[_-]/g, ' ')
    .replace(/([a-z])([A-Z])/g, '$1 $2')
    .replace(/^\w/, (c) => c.toUpperCase());
}

function isGroupSaved(grp) {
  if (!elementsStore.selectedIllustration) return false;
  return elementsStore.components.some(
    (c) => c.illustration_id === elementsStore.selectedIllustration.id &&
           (c.title.toLowerCase() === grp.name.toLowerCase() || c.title.toLowerCase() === grp.id.toLowerCase())
  );
}

function toggleGroupSelect(id, e) {
  e.stopPropagation();
  const set = new Set(selectedGroupIds.value);
  if (set.has(id)) {
    set.delete(id);
  } else {
    set.add(id);
  }
  selectedGroupIds.value = set;
}

function toggleSelectAll() {
  if (selectedGroupIds.value.size === detectedGroups.value.length) {
    selectedGroupIds.value = new Set();
  } else {
    selectedGroupIds.value = new Set(detectedGroups.value.map((g) => g.id));
  }
}

function handleSvgMouseMove(e) {
  const target = e.target;
  if (!target || !svgContainerRef.value) return;

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

  const group = target.closest('g[id], g[data-name]') || target;
  selectTargetElement(group);
}

function selectTargetElement(element) {
  if (!element || !svgContainerRef.value) return;
  const svgRoot = svgContainerRef.value.querySelector('svg');
  if (!svgRoot) return;

  svgRoot.querySelectorAll('.svg-inspect-active').forEach((el) => {
    el.classList.remove('svg-inspect-active');
  });

  element.classList.add('svg-inspect-active');

  const data = extractNodeData(element, svgRoot);
  if (data) {
    extractedData.value = data;
    componentTitle.value = data.title;
    componentCategory.value = data.category || 'Предметы';
    componentTags.value = '';
    previewSvgHtml.value = buildStandaloneSvg(data.svgContent, data.viewBox, data.defs);
    saveSuccess.value = false;
    canvasAddedToast.value = false;
  }
}

// Single component save
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
      canvasAddedToast.value = true;
      setTimeout(() => {
        canvasAddedToast.value = false;
      }, 3000);
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

// BATCH EXTRACTION: Extract all (or selected) groups at once into library and/or canvas
async function handleBatchExtract(andAddToCanvas = false) {
  if (detectedGroups.value.length === 0 || !svgContainerRef.value) return;

  const svgRoot = svgContainerRef.value.querySelector('svg');
  if (!svgRoot) return;

  const groupsToProcess = detectedGroups.value.filter((g) => 
    selectedGroupIds.value.size === 0 || selectedGroupIds.value.has(g.id)
  );

  if (groupsToProcess.length === 0) {
    alert('Пожалуйста, выберите хотя бы один элемент для извлечения');
    return;
  }

  isBatchProcessing.value = true;
  let processedCount = 0;
  const addedComponents = [];

  try {
    for (let i = 0; i < groupsToProcess.length; i++) {
      const grp = groupsToProcess[i];
      batchProgressMessage.value = `Извлечение ${i + 1} из ${groupsToProcess.length}: «${grp.name}»...`;

      // Extract node data
      const data = extractNodeData(grp.element, svgRoot);
      if (!data) continue;

      // Check if already in library
      let comp = elementsStore.components.find(
        (c) => c.illustration_id === elementsStore.selectedIllustration?.id &&
               c.title.toLowerCase() === data.title.toLowerCase()
      );

      // If not yet in library, save to DB
      if (!comp) {
        const payload = {
          illustration_id: elementsStore.selectedIllustration?.id || null,
          title: data.title,
          category: data.category || 'Предметы',
          svg_content: data.svgContent,
          view_box: data.viewBox,
          width: data.width,
          height: data.height,
          tags: [elementsStore.selectedIllustration?.category || 'Объекты'],
        };
        comp = await elementsStore.saveExtractedComponent(payload);
      }

      addedComponents.push(comp);
      processedCount++;
    }

    // If requested, place all extracted components onto canvas in a clean layout
    if (andAddToCanvas) {
      const startX = 80;
      const startY = 120;
      const colWidth = 240;
      const rowHeight = 220;
      const cols = 4;

      addedComponents.forEach((comp, idx) => {
        const col = idx % cols;
        const row = Math.floor(idx / cols);
        const x = startX + col * colWidth;
        const y = startY + row * rowHeight;
        canvasStore.addElement(comp, x, y);
      });
    }

    batchSuccessToast.value = andAddToCanvas
      ? `Успешно извлечено и добавлено на холст ${processedCount} элементов!`
      : `Успешно сохранено в библиотеку ${processedCount} элементов!`;

    setTimeout(() => {
      batchSuccessToast.value = '';
    }, 4500);
  } catch (err) {
    console.error('Ошибка пакетного извлечения:', err);
    alert('Произошла ошибка при пакетном извлечении');
  } finally {
    isBatchProcessing.value = false;
    batchProgressMessage.value = '';
  }
}
</script>

<template>
  <div 
    v-if="elementsStore.isInspectorOpen && elementsStore.selectedIllustration"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/85 backdrop-blur-md animate-fade-in select-none"
  >
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-7xl h-[92vh] flex flex-col shadow-2xl overflow-hidden relative">
      
      <!-- Modal Header (shrink-0) with Prev / Next navigation -->
      <div class="px-6 py-3.5 border-b border-slate-800 flex items-center justify-between bg-slate-900 shrink-0">
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
            <Scissors class="w-4 h-4" />
          </div>
          <div>
            <div class="flex items-center space-x-2">
              <h2 class="text-sm sm:text-base font-bold text-white truncate max-w-md">
                Инспектор SVG: {{ elementsStore.selectedIllustration.title }}
              </h2>
              <span class="text-xs px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700">
                {{ elementsStore.selectedIllustration.category }}
              </span>
            </div>
            <p class="text-[11px] text-slate-400">
              Выделяйте отдельные объекты кликом или извлекайте сразу все элементы в 1 клик
            </p>
          </div>
        </div>

        <!-- Illustration Quick Pager (< / >) & Close -->
        <div class="flex items-center space-x-2">
          <!-- Pager buttons -->
          <div class="flex items-center bg-slate-800/80 border border-slate-700 rounded-xl p-0.5">
            <button 
              @click="goToPrevIllustration"
              :disabled="!hasPrev"
              title="Предыдущая иллюстрация"
              class="p-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition disabled:opacity-30 disabled:hover:bg-transparent"
            >
              <ChevronLeft class="w-4 h-4" />
            </button>
            <span class="text-xs font-mono px-2 text-slate-400">
              {{ currentIllustrationIndex + 1 }} / {{ elementsStore.illustrations.length }}
            </span>
            <button 
              @click="goToNextIllustration"
              :disabled="!hasNext"
              title="Следующая иллюстрация"
              class="p-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition disabled:opacity-30 disabled:hover:bg-transparent"
            >
              <ChevronRight class="w-4 h-4" />
            </button>
          </div>

          <!-- Go to Canvas Shortcut -->
          <button 
            @click="elementsStore.isInspectorOpen = false"
            class="flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition cursor-pointer"
          >
            <span>На холст</span>
            <ExternalLink class="w-3.5 h-3.5 text-blue-400" />
          </button>

          <button 
            @click="elementsStore.isInspectorOpen = false"
            class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition"
          >
            <X class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Batch Success Notification Banner -->
      <div 
        v-if="batchSuccessToast"
        class="bg-emerald-500/20 border-b border-emerald-500/30 px-6 py-2.5 text-xs text-emerald-300 flex items-center justify-between shrink-0 animate-fade-in"
      >
        <div class="flex items-center space-x-2">
          <CheckCircle2 class="w-4 h-4 text-emerald-400" />
          <span class="font-medium">{{ batchSuccessToast }}</span>
        </div>
        <button 
          @click="elementsStore.isInspectorOpen = false"
          class="underline hover:text-white text-xs cursor-pointer"
        >
          Закрыть инспектор и перейти к композиции →
        </button>
      </div>

      <!-- Main Workspace: Left Inspector + Right Preview/Save Panel -->
      <div class="flex-1 min-h-0 overflow-hidden grid grid-cols-1 lg:grid-cols-12 divide-y lg:divide-y-0 lg:divide-x divide-slate-800">
        
        <!-- Left: Interactive SVG Inspector View & Detected Groups (7 cols) -->
        <div class="lg:col-span-7 p-4 sm:p-5 flex flex-col bg-slate-950/40 overflow-y-auto min-h-0">
          
          <!-- Instructions & Quick Hover Bar -->
          <div class="mb-3 px-3.5 py-2 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center justify-between text-xs shrink-0">
            <div class="flex items-center space-x-2 text-slate-300">
              <MousePointer class="w-3.5 h-3.5 text-blue-400 animate-pulse" />
              <span>
                {{ hoveredNodeName ? `Объект под курсором: ${hoveredNodeName}` : 'Кликните по любому объекту на изображении' }}
              </span>
            </div>
            <span class="text-[11px] text-slate-400 font-mono">
              {{ detectedGroups.length }} элементов найдено
            </span>
          </div>

          <!-- SVG Canvas Wrapper -->
          <div 
            ref="svgContainerRef"
            @mousemove="handleSvgMouseMove"
            @mouseleave="handleSvgMouseLeave"
            @click="handleSvgClick"
            class="flex-1 bg-slate-900/80 rounded-xl border border-slate-800 p-4 flex items-center justify-center relative select-none cursor-pointer overflow-hidden min-h-[340px]"
            v-html="elementsStore.selectedIllustration.svg_content"
          ></div>

          <!-- BATCH ACTIONS TOOLBAR: Extract all at once -->
          <div class="mt-4 p-3 bg-slate-900/90 border border-slate-800/90 rounded-xl space-y-3 shrink-0">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <div class="flex items-center space-x-2">
                <label class="flex items-center space-x-1.5 cursor-pointer text-xs font-semibold text-slate-200">
                  <input 
                    type="checkbox" 
                    :checked="selectedGroupIds.size === detectedGroups.length && detectedGroups.length > 0"
                    @change="toggleSelectAll"
                    class="rounded bg-slate-800 border-slate-700 text-blue-600 focus:ring-0"
                  />
                  <span>Выбрать все ({{ detectedGroups.length }})</span>
                </label>
                <span class="text-[11px] text-slate-400">
                  Выбрано: {{ selectedGroupIds.size }}
                </span>
              </div>

              <!-- Main Batch CTA: Extract All / Selected -->
              <div class="flex items-center space-x-2">
                <button 
                  @click="handleBatchExtract(false)"
                  :disabled="isBatchProcessing || detectedGroups.length === 0"
                  class="flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition active:scale-95 disabled:opacity-50 cursor-pointer"
                >
                  <FolderPlus class="w-3.5 h-3.5 text-blue-400" />
                  <span>Сохранить все в библиотеку</span>
                </button>

                <!-- Batch Add to Canvas (Super convenient!) -->
                <button 
                  @click="handleBatchExtract(true)"
                  :disabled="isBatchProcessing || detectedGroups.length === 0"
                  class="flex items-center space-x-1.5 px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-semibold shadow-md shadow-emerald-500/20 transition active:scale-95 disabled:opacity-50 cursor-pointer"
                >
                  <Loader2 v-if="isBatchProcessing" class="w-3.5 h-3.5 animate-spin" />
                  <Zap v-else class="w-3.5 h-3.5 text-amber-300" />
                  <span>Добавить ВСЕ на холст и в библиотеку</span>
                </button>
              </div>
            </div>

            <!-- Batch Progress Bar if active -->
            <div v-if="isBatchProcessing" class="text-xs text-emerald-400 flex items-center space-x-2 pt-1 font-mono">
              <Loader2 class="w-3.5 h-3.5 animate-spin" />
              <span>{{ batchProgressMessage }}</span>
            </div>

            <!-- List of Detected Groups with Status Badges -->
            <div class="flex flex-wrap gap-2 max-h-36 overflow-y-auto pr-1 pt-1">
              <div 
                v-for="grp in detectedGroups" 
                :key="grp.id"
                @click="selectTargetElement(grp.element)"
                :class="extractedData && extractedData.title === grp.name 
                  ? 'bg-blue-600 text-white border-blue-500 shadow-md shadow-blue-500/20' 
                  : 'bg-slate-950/80 text-slate-300 hover:bg-slate-800/80 border-slate-800'"
                class="text-xs px-2.5 py-1.5 rounded-lg border font-medium transition flex items-center space-x-2 cursor-pointer active:scale-95"
              >
                <!-- Group Checkbox -->
                <input 
                  type="checkbox" 
                  :checked="selectedGroupIds.has(grp.id)"
                  @click="toggleGroupSelect(grp.id, $event)"
                  class="rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-0"
                />

                <span>{{ grp.name }}</span>

                <!-- Already saved indicator -->
                <span 
                  v-if="isGroupSaved(grp)" 
                  class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-semibold"
                >
                  ✓ В библиотеке
                </span>
                <span v-else class="text-[9px] text-slate-400 opacity-60">
                  + Не извлечён
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Single Extracted Component Preview & Form (5 cols) -->
        <div class="lg:col-span-5 p-5 flex flex-col justify-between bg-slate-900/90 overflow-y-auto min-h-0">
          <div v-if="extractedData" class="space-y-4">
            
            <!-- Header of extraction -->
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
              <div>
                <h3 class="text-sm font-bold text-white">Текущий выделенный объект</h3>
                <p class="text-xs text-slate-400">Изолированный векторный фрагмент со своим viewBox</p>
              </div>
              <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                100% Вектор
              </span>
            </div>

            <!-- Single Component SVG Preview -->
            <div class="bg-slate-950/80 rounded-xl border border-slate-800 p-4 flex flex-col items-center justify-center relative min-h-[180px]">
              <div class="w-36 h-36 flex items-center justify-center svg-thumb-container" v-html="previewSvgHtml"></div>
              
              <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between text-[10px] text-slate-400 font-mono px-2">
                <span>Размеры: {{ extractedData.width }} × {{ extractedData.height }} px</span>
                <span>viewBox: {{ extractedData.viewBox }}</span>
              </div>
            </div>

            <!-- Single Component Config Form -->
            <div class="space-y-3 pt-1">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">
                  Название компонента
                </label>
                <input 
                  v-model="componentTitle" 
                  type="text" 
                  placeholder="Например: Бариста"
                  class="w-full bg-slate-800/80 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:border-blue-500 focus:outline-none transition"
                />
              </div>

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
              Кликните по любому объекту на иллюстрации слева или воспользуйтесь кнопкой «Добавить ВСЕ на холст»
            </p>
          </div>

          <!-- Single Item Action Buttons Footer (DOES NOT CLOSE MODAL AUTOMATICALLY!) -->
          <div class="pt-4 border-t border-slate-800 space-y-2">
            <!-- Canvas added toast -->
            <div 
              v-if="canvasAddedToast" 
              class="p-2 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs flex items-center justify-between animate-fade-in"
            >
              <span class="flex items-center space-x-1.5">
                <Check class="w-4 h-4 text-emerald-400" />
                <span>Элемент добавлен на холст! Инспектор остаётся открытым.</span>
              </span>
              <button 
                @click="elementsStore.isInspectorOpen = false"
                class="underline font-semibold hover:text-white"
              >
                К холсту →
              </button>
            </div>

            <!-- Save to Library button -->
            <button 
              @click="handleSaveComponent(false)"
              :disabled="!extractedData || isSaving"
              class="w-full flex items-center justify-center space-x-2 text-xs font-semibold py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white shadow-lg shadow-blue-500/20 transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
            >
              <Check v-if="saveSuccess" class="w-4 h-4 text-emerald-300" />
              <FolderPlus v-else class="w-4 h-4" />
              <span>{{ saveSuccess ? 'Сохранено в библиотеку!' : 'Сохранить выделенный в библиотеку' }}</span>
            </button>

            <!-- Save & Add to Canvas button -->
            <button 
              @click="handleAddDirectlyToCanvas"
              :disabled="!extractedData || isSaving"
              class="w-full flex items-center justify-center space-x-2 text-xs font-semibold py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
            >
              <PlusCircle class="w-4 h-4 text-emerald-400" />
              <span>Сохранить выделенный и добавить на холст</span>
            </button>
          </div>

        </div>

      </div>
    </div>
  </div>
</template>
