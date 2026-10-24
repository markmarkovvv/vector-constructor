<script setup>
import { ref, computed } from 'vue';
import { useCanvasStore } from '../../stores/canvasStore';
import { 
  composeByMetaphor, 
  composeByComponents, 
  METAPHOR_PRESETS 
} from '../../utils/autoComposer';
import { ISOMETRIC_COMPONENTS } from '../../utils/isometricLibrary';
import { CANVAS_FORMATS } from '../../utils/canvasFormats';
import { COLOR_SCHEMES } from '../../utils/colorSchemes';
import confetti from 'canvas-confetti';
import { 
  X, 
  Wand2, 
  Sparkles, 
  Check, 
  Layers, 
  Square, 
  RectangleHorizontal, 
  RectangleVertical,
  BookOpen,
  ArrowRight,
  SlidersHorizontal
} from 'lucide-vue-next';

const canvasStore = useCanvasStore();

const activeTab = ref('metaphor'); // 'metaphor' | 'components'
const metaphorInput = ref('Учёба и образование');
const selectedFormatId = ref(canvasStore.currentFormatId || 'square');
const selectedDetailLevel = ref(canvasStore.currentDetailLevel || 'L');

// Scenario 2 state
const selectedComponentIds = ref(['iso_book', 'iso_lamp', 'iso_headphones', 'iso_pencil']);
const includeBackground = ref(true);

function selectMetaphorPreset(preset) {
  metaphorInput.value = preset.title;
  selectedFormatId.value = preset.recommendedFormat;
}

function toggleComponentSelection(id) {
  if (selectedComponentIds.value.includes(id)) {
    selectedComponentIds.value = selectedComponentIds.value.filter((i) => i !== id);
  } else {
    selectedComponentIds.value.push(id);
  }
}

function selectAllComponents() {
  selectedComponentIds.value = ISOMETRIC_COMPONENTS.map((c) => c.id);
}

function clearComponentSelection() {
  selectedComponentIds.value = [];
}

// Execute Scenario 1: Generate by Metaphor
function handleGenerateByMetaphor() {
  if (!metaphorInput.value.trim()) return;

  const result = composeByMetaphor(metaphorInput.value, {
    formatId: selectedFormatId.value,
    detailLevel: selectedDetailLevel.value,
  });

  if (result) {
    canvasStore.applyAutoComposition(result);
    canvasStore.isSmartComposerOpen = false;

    // Confetti celebration
    confetti({
      particleCount: 90,
      spread: 60,
      origin: { y: 0.6 },
    });
  }
}

// Execute Scenario 2: Generate by Components List
function handleGenerateByComponents() {
  if (selectedComponentIds.value.length === 0) {
    alert('Пожалуйста, выберите хотя бы один компонент');
    return;
  }

  const result = composeByComponents(selectedComponentIds.value, {
    formatId: selectedFormatId.value,
    detailLevel: selectedDetailLevel.value,
    includeBackground: includeBackground.value,
    schemeId: canvasStore.currentSchemeId,
  });

  if (result) {
    canvasStore.applyAutoComposition(result);
    canvasStore.isSmartComposerOpen = false;

    confetti({
      particleCount: 90,
      spread: 60,
      origin: { y: 0.6 },
    });
  }
}
</script>

<template>
  <div 
    v-if="canvasStore.isSmartComposerOpen"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fade-in select-none"
  >
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden relative">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/90 shrink-0">
        <div class="flex items-center space-x-3">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-violet-600 to-pink-600 flex items-center justify-center text-white shadow-lg shadow-purple-500/20">
            <Wand2 class="w-4 h-4 animate-pulse text-amber-300" />
          </div>
          <div>
            <div class="flex items-center space-x-2">
              <h2 class="text-base font-bold text-white">Умная автосборка композиции</h2>
              <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/20">
                Сценарии 1 & 2
              </span>
            </div>
            <p class="text-xs text-slate-400">
              Автоматическая расстановка элементов по правилам композиции и дизайн-системы
            </p>
          </div>
        </div>

        <button 
          @click="canvasStore.isSmartComposerOpen = false"
          class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Scenarios Tab Switcher -->
      <div class="px-6 pt-3 border-b border-slate-800 flex space-x-2 bg-slate-950/40 shrink-0">
        <button 
          @click="activeTab = 'metaphor'"
          :class="activeTab === 'metaphor' 
            ? 'text-purple-400 border-b-2 border-purple-500 font-semibold' 
            : 'text-slate-400 hover:text-slate-200'"
          class="pb-2.5 px-3 text-xs flex items-center space-x-2 transition cursor-pointer"
        >
          <Sparkles class="w-3.5 h-3.5" />
          <span>Сценарий 1: По метафоре / теме</span>
        </button>

        <button 
          @click="activeTab = 'components'"
          :class="activeTab === 'components' 
            ? 'text-purple-400 border-b-2 border-purple-500 font-semibold' 
            : 'text-slate-400 hover:text-slate-200'"
          class="pb-2.5 px-3 text-xs flex items-center space-x-2 transition cursor-pointer"
        >
          <SlidersHorizontal class="w-3.5 h-3.5" />
          <span>Сценарий 2: По списку компонентов</span>
        </button>
      </div>

      <!-- Modal Body (Scrollable) -->
      <div class="p-6 overflow-y-auto space-y-5 text-xs text-slate-300">
        
        <!-- Format & Detail Level Row -->
        <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-slate-950/60 border border-slate-800">
          <div>
            <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">
              Формат холста (Figma спецификация)
            </label>
            <div class="grid grid-cols-3 gap-1">
              <button 
                v-for="fmt in CANVAS_FORMATS" 
                :key="fmt.id"
                @click="selectedFormatId = fmt.id"
                :class="selectedFormatId === fmt.id 
                  ? 'bg-blue-600 text-white font-bold border-blue-500 shadow-sm' 
                  : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700 border-slate-700'"
                class="py-1.5 px-1 rounded-lg border text-[11px] font-mono flex flex-col items-center justify-center space-y-0.5 cursor-pointer transition"
              >
                <span>{{ fmt.ratio }}</span>
                <span class="text-[9px] opacity-75 font-sans">{{ fmt.name }}</span>
              </button>
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">
              Уровень детализации элементов
            </label>
            <div class="grid grid-cols-2 gap-1.5">
              <button 
                @click="selectedDetailLevel = 'L'"
                :class="selectedDetailLevel === 'L' 
                  ? 'bg-indigo-600 text-white font-bold border-indigo-500 shadow-sm' 
                  : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700 border-slate-700'"
                class="py-2 px-2 rounded-lg border text-center cursor-pointer transition"
              >
                <div class="font-bold text-xs">Размер L</div>
                <div class="text-[9px] opacity-75">Высокая детализация</div>
              </button>
              <button 
                @click="selectedDetailLevel = 'S'"
                :class="selectedDetailLevel === 'S' 
                  ? 'bg-indigo-600 text-white font-bold border-indigo-500 shadow-sm' 
                  : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700 border-slate-700'"
                class="py-2 px-2 rounded-lg border text-center cursor-pointer transition"
              >
                <div class="font-bold text-xs">Размер S</div>
                <div class="text-[9px] opacity-75">Упрощенная графика</div>
              </button>
            </div>
          </div>
        </div>

        <!-- TAB 1: SCENARIO 1 (METAPHOR / TITLE) -->
        <div v-if="activeTab === 'metaphor'" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-200 mb-1.5">
              Задайте метафору или тему композиции:
            </label>
            <div class="relative">
              <input 
                v-model="metaphorInput"
                type="text"
                placeholder="Например: Учёба и знания, Финтех и стартап, Творческий вечер..."
                @keydown.enter="handleGenerateByMetaphor"
                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition"
              />
              <button 
                @click="handleGenerateByMetaphor"
                class="absolute right-1.5 top-1.5 bottom-1.5 px-3 bg-purple-600 hover:bg-purple-500 text-white rounded-lg font-semibold flex items-center space-x-1 cursor-pointer transition"
              >
                <span>Собрать</span>
                <ArrowRight class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>

          <!-- Quick Presets -->
          <div>
            <span class="text-[11px] font-semibold text-slate-400 block mb-2">
              Быстрые пресеты по темам:
            </span>
            <div class="flex flex-wrap gap-2">
              <button 
                v-for="preset in METAPHOR_PRESETS" 
                :key="preset.id"
                @click="selectMetaphorPreset(preset)"
                :class="metaphorInput === preset.title 
                  ? 'bg-purple-600/30 text-purple-200 border-purple-500/60 ring-1 ring-purple-500/40' 
                  : 'bg-slate-800/80 text-slate-300 hover:bg-slate-800 border-slate-700'"
                class="text-xs px-3 py-1.5 rounded-lg border flex items-center space-x-1.5 transition cursor-pointer active:scale-95"
              >
                <span>{{ preset.title }}</span>
              </button>
            </div>
          </div>

          <div class="p-3.5 rounded-xl bg-purple-950/20 border border-purple-500/20 text-purple-200/90 space-y-1 text-[11px]">
            <p class="font-semibold text-purple-300 flex items-center space-x-1.5">
              <span>✨ Как работает Сценарий 1:</span>
            </p>
            <p>
              Программа анализирует метафору, выбирает главные (полноцветные) объекты, окружает их фоновыми (монохромными) объектами в тон палитры и выстраивает баланс композиции по правилам дизайн-системы.
            </p>
          </div>
        </div>

        <!-- TAB 2: SCENARIO 2 (COMPONENTS LIST) -->
        <div v-else class="space-y-4">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-slate-200">
              Выберите нужные компоненты (выбрано: {{ selectedComponentIds.length }}):
            </span>
            <div class="space-x-2 text-[11px]">
              <button @click="selectAllComponents" class="text-blue-400 hover:underline cursor-pointer">Выбрать все</button>
              <span class="text-slate-600">·</span>
              <button @click="clearComponentSelection" class="text-slate-400 hover:underline cursor-pointer">Сбросить</button>
            </div>
          </div>

          <!-- Components Selection Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-56 overflow-y-auto pr-1">
            <div 
              v-for="comp in ISOMETRIC_COMPONENTS" 
              :key="comp.id"
              @click="toggleComponentSelection(comp.id)"
              :class="selectedComponentIds.includes(comp.id) 
                ? 'bg-purple-900/40 border-purple-500 text-white shadow-sm shadow-purple-500/20' 
                : 'bg-slate-950/60 border-slate-800 text-slate-300 hover:bg-slate-800/60'"
              class="p-2 rounded-xl border flex items-center space-x-2.5 cursor-pointer transition"
            >
              <div 
                :class="selectedComponentIds.includes(comp.id) ? 'bg-purple-500 text-white' : 'bg-slate-800 text-transparent'"
                class="w-4 h-4 rounded border border-slate-700 flex items-center justify-center shrink-0 transition"
              >
                <Check class="w-3 h-3" />
              </div>
              <div class="truncate text-xs font-medium">{{ comp.title }}</div>
            </div>
          </div>

          <!-- Include Background Option -->
          <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950/60 border border-slate-800">
            <div>
              <span class="block text-xs font-semibold text-slate-200">
                Включать фоновые объекты (монохром)
              </span>
              <span class="text-[10px] text-slate-400">
                Создаёт глубину сцены по правилам спецификации Figma
              </span>
            </div>
            <input 
              v-model="includeBackground"
              type="checkbox"
              class="w-4 h-4 rounded bg-slate-800 border-slate-700 text-purple-600 focus:ring-0 cursor-pointer"
            />
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/90 flex items-center justify-between shrink-0">
        <button 
          @click="canvasStore.isSmartComposerOpen = false"
          class="px-4 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white transition cursor-pointer"
        >
          Отмена
        </button>

        <button 
          @click="activeTab === 'metaphor' ? handleGenerateByMetaphor() : handleGenerateByComponents()"
          class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-gradient-to-r from-violet-600 via-purple-600 to-pink-600 hover:from-violet-500 hover:to-pink-500 text-white shadow-lg shadow-purple-500/25 transition active:scale-95 flex items-center space-x-2 cursor-pointer"
        >
          <Sparkles class="w-4 h-4 text-amber-300" />
          <span>Собрать готовую композицию</span>
        </button>
      </div>

    </div>
  </div>
</template>
