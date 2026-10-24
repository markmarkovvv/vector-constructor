<script setup>
import { ref } from 'vue';
import { useCanvasStore } from '../../stores/canvasStore';
import { 
  Layers, 
  Sliders, 
  Eye, 
  EyeOff, 
  Lock, 
  Unlock, 
  ArrowUp, 
  ArrowDown, 
  ChevronsUp, 
  ChevronsDown, 
  Copy, 
  Trash2, 
  FlipHorizontal, 
  FlipVertical, 
  RotateCw, 
  Palette,
  Maximize
} from 'lucide-vue-next';

const canvasStore = useCanvasStore();
const activeTab = ref('layers'); // 'layers' | 'properties'

const bgPalette = [
  { name: 'Белый', color: '#ffffff' },
  { name: 'Светло-серый', color: '#f8fafc' },
  { name: 'Темный сланец', color: '#0f172a' },
  { name: 'Теплый песок', color: '#fef3c7' },
  { name: 'Небесный', color: '#e0f2fe' },
  { name: 'Мятный', color: '#ecfdf5' },
  { name: 'Нежно-розовый', color: '#fdf2f8' },
];

function handleLayerClick(id) {
  canvasStore.selectElement(id);
  activeTab.value = 'properties';
}

function toggleVisibility(e, item) {
  e.stopPropagation();
  canvasStore.updateElement(item.id, { visible: item.visible === false });
}

function toggleLock(e, item) {
  e.stopPropagation();
  canvasStore.updateElement(item.id, { locked: !item.locked });
}
</script>

<template>
  <aside class="w-72 border-l border-slate-800 bg-slate-900/90 flex flex-col z-20 select-none">
    <!-- Tabs Header -->
    <div class="flex border-b border-slate-800 bg-slate-950/30">
      <button 
        @click="activeTab = 'layers'"
        :class="activeTab === 'layers' ? 'text-blue-400 border-b-2 border-blue-500 bg-slate-900/70 font-semibold' : 'text-slate-400 hover:text-slate-200'"
        class="flex-1 py-3 text-xs flex items-center justify-center space-x-1.5 transition"
      >
        <Layers class="w-4 h-4" />
        <span>Слои ({{ canvasStore.elements.length }})</span>
      </button>

      <button 
        @click="activeTab = 'properties'"
        :class="activeTab === 'properties' ? 'text-blue-400 border-b-2 border-blue-500 bg-slate-900/70 font-semibold' : 'text-slate-400 hover:text-slate-200'"
        class="flex-1 py-3 text-xs flex items-center justify-center space-x-1.5 transition"
      >
        <Sliders class="w-4 h-4" />
        <span>Свойства</span>
      </button>
    </div>

    <!-- TAB 1: LAYERS -->
    <div v-if="activeTab === 'layers'" class="flex-1 flex flex-col overflow-hidden">
      <!-- Layers Action Toolbar -->
      <div v-if="canvasStore.selectedId" class="p-2 border-b border-slate-800/80 bg-slate-950/40 flex items-center justify-around text-slate-400">
        <button 
          @click="canvasStore.bringToFront(canvasStore.selectedId)" 
          title="На передний план"
          class="p-1 rounded hover:text-white hover:bg-slate-800 transition"
        >
          <ChevronsUp class="w-3.5 h-3.5" />
        </button>
        <button 
          @click="canvasStore.bringForward(canvasStore.selectedId)" 
          title="Поднять на слой выше"
          class="p-1 rounded hover:text-white hover:bg-slate-800 transition"
        >
          <ArrowUp class="w-3.5 h-3.5" />
        </button>
        <button 
          @click="canvasStore.sendBackward(canvasStore.selectedId)" 
          title="Опустить на слой ниже"
          class="p-1 rounded hover:text-white hover:bg-slate-800 transition"
        >
          <ArrowDown class="w-3.5 h-3.5" />
        </button>
        <button 
          @click="canvasStore.sendToBack(canvasStore.selectedId)" 
          title="На задний план"
          class="p-1 rounded hover:text-white hover:bg-slate-800 transition"
        >
          <ChevronsDown class="w-3.5 h-3.5" />
        </button>
        <div class="h-3 w-px bg-slate-800"></div>
        <button 
          @click="canvasStore.duplicateSelected()" 
          title="Дублировать"
          class="p-1 rounded hover:text-blue-400 hover:bg-slate-800 transition"
        >
          <Copy class="w-3.5 h-3.5" />
        </button>
        <button 
          @click="canvasStore.deleteSelected()" 
          title="Удалить"
          class="p-1 rounded hover:text-red-400 hover:bg-slate-800 transition"
        >
          <Trash2 class="w-3.5 h-3.5" />
        </button>
      </div>

      <!-- Layers List -->
      <div class="flex-1 overflow-y-auto p-2 space-y-1">
        <div 
          v-for="item in canvasStore.layersSorted" 
          :key="item.id"
          @click="handleLayerClick(item.id)"
          :class="canvasStore.selectedId === item.id 
            ? 'bg-blue-600/20 border-blue-500/50 text-white' 
            : 'bg-slate-950/40 border-slate-800/80 text-slate-300 hover:bg-slate-800/50 hover:border-slate-700'"
          class="group px-2.5 py-2 rounded-xl border flex items-center justify-between transition cursor-pointer text-xs"
        >
          <div class="flex items-center space-x-2.5 min-w-0">
            <!-- Mini SVG preview -->
            <div class="w-7 h-7 rounded bg-slate-900 border border-slate-800 p-0.5 flex items-center justify-center flex-shrink-0">
              <svg 
                :viewBox="item.viewBox" 
                class="w-full h-full pointer-events-none"
                v-html="item.svgContent"
              ></svg>
            </div>
            <div class="truncate">
              <div class="flex items-center space-x-1.5">
                <span class="font-medium truncate block">{{ item.title }}</span>
                <span 
                  @click.stop="canvasStore.toggleElementState(item.id)"
                  :class="item.isBackground 
                    ? 'bg-purple-500/20 text-purple-300 border-purple-500/30' 
                    : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30'"
                  class="text-[9px] px-1.5 py-0.2 rounded border font-semibold shrink-0 cursor-pointer hover:scale-105 transition"
                  :title="item.isBackground ? 'Фоновый объект (монохром)' : 'Главный объект (полноцветный)'"
                >
                  {{ item.isBackground ? 'Фон' : 'Главный' }}
                </span>
              </div>
              <span class="text-[9px] text-slate-400 font-mono">z: {{ item.zIndex }} · {{ item.detailLevel || 'L' }}</span>
            </div>
          </div>

          <!-- Quick Visibility & Lock Controls -->
          <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition">
            <button 
              @click="toggleVisibility($event, item)" 
              class="p-1 rounded hover:bg-slate-700 text-slate-400 hover:text-white"
            >
              <EyeOff v-if="item.visible === false" class="w-3 h-3 text-red-400" />
              <Eye v-else class="w-3 h-3" />
            </button>
            <button 
              @click="toggleLock($event, item)" 
              class="p-1 rounded hover:bg-slate-700 text-slate-400 hover:text-white"
            >
              <Lock v-if="item.locked" class="w-3 h-3 text-amber-400" />
              <Unlock v-else class="w-3 h-3" />
            </button>
          </div>
        </div>

        <div v-if="canvasStore.elements.length === 0" class="text-center py-12 text-slate-500 text-xs">
          <Layers class="w-8 h-8 mx-auto mb-2 opacity-30 text-slate-400" />
          <p>Слоёв пока нет</p>
          <p class="text-[11px] text-slate-500 mt-1">Добавьте элементы из левой библиотеки</p>
        </div>
      </div>
    </div>

    <!-- TAB 2: PROPERTIES -->
    <div v-else class="flex-1 overflow-y-auto p-4 space-y-5 text-xs">
      
      <!-- Selected Object Properties -->
      <div v-if="canvasStore.selectedElement" class="space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
          <span class="font-bold text-white text-sm truncate">
            {{ canvasStore.selectedElement.title }}
          </span>
          <span class="text-[10px] font-mono text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded">
            Объект выбран
          </span>
        </div>

        <!-- Object State & Detail Level (Figma requirements) -->
        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800 space-y-2.5">
          <div>
            <label class="block font-semibold text-slate-400 text-[11px] mb-1.5">
              Состояние объекта (Figma ТЗ)
            </label>
            <div class="grid grid-cols-2 gap-1.5">
              <button 
                @click="canvasStore.updateElement(canvasStore.selectedId, { isBackground: false })"
                :class="!canvasStore.selectedElement.isBackground 
                  ? 'bg-emerald-600 text-white font-bold border-emerald-500 shadow-sm' 
                  : 'bg-slate-800 text-slate-400 hover:text-white border-slate-700'"
                class="py-1.5 px-2 rounded-lg border text-[11px] flex items-center justify-center space-x-1 transition cursor-pointer"
              >
                <span>🌟 Главный (Цвет)</span>
              </button>
              <button 
                @click="canvasStore.updateElement(canvasStore.selectedId, { isBackground: true })"
                :class="canvasStore.selectedElement.isBackground 
                  ? 'bg-purple-600 text-white font-bold border-purple-500 shadow-sm' 
                  : 'bg-slate-800 text-slate-400 hover:text-white border-slate-700'"
                class="py-1.5 px-2 rounded-lg border text-[11px] flex items-center justify-center space-x-1 transition cursor-pointer"
              >
                <span>🌫️ Фоновый</span>
              </button>
            </div>
          </div>

          <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
            <span class="text-[11px] text-slate-400">Детализация:</span>
            <div class="flex items-center space-x-1 bg-slate-900 p-0.5 rounded border border-slate-800">
              <button 
                @click="canvasStore.toggleElementDetail(canvasStore.selectedId)"
                :class="(canvasStore.selectedElement.detailLevel || 'L') === 'L' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-white'"
                class="px-2 py-0.5 rounded text-[10px] font-mono transition cursor-pointer"
              >
                Размер L
              </button>
              <button 
                @click="canvasStore.toggleElementDetail(canvasStore.selectedId)"
                :class="(canvasStore.selectedElement.detailLevel || 'L') === 'S' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:text-white'"
                class="px-2 py-0.5 rounded text-[10px] font-mono transition cursor-pointer"
              >
                Размер S
              </button>
            </div>
          </div>
        </div>

        <!-- Position X, Y -->
        <div>
          <label class="block font-semibold text-slate-400 mb-1.5">Позиция (X, Y)</label>
          <div class="grid grid-cols-2 gap-2">
            <div class="flex items-center bg-slate-950/60 border border-slate-800 rounded-lg px-2 py-1.5">
              <span class="text-slate-500 font-mono text-[11px] mr-1.5">X:</span>
              <input 
                :value="Math.round(canvasStore.selectedElement.x)"
                @input="canvasStore.updateElement(canvasStore.selectedId, { x: +$event.target.value })"
                type="number"
                class="w-full bg-transparent text-slate-200 outline-none text-xs"
              />
            </div>
            <div class="flex items-center bg-slate-950/60 border border-slate-800 rounded-lg px-2 py-1.5">
              <span class="text-slate-500 font-mono text-[11px] mr-1.5">Y:</span>
              <input 
                :value="Math.round(canvasStore.selectedElement.y)"
                @input="canvasStore.updateElement(canvasStore.selectedId, { y: +$event.target.value })"
                type="number"
                class="w-full bg-transparent text-slate-200 outline-none text-xs"
              />
            </div>
          </div>
        </div>

        <!-- Size Width, Height -->
        <div>
          <label class="block font-semibold text-slate-400 mb-1.5">Размеры (Ширина, Высота)</label>
          <div class="grid grid-cols-2 gap-2">
            <div class="flex items-center bg-slate-950/60 border border-slate-800 rounded-lg px-2 py-1.5">
              <span class="text-slate-500 font-mono text-[11px] mr-1.5">W:</span>
              <input 
                :value="Math.round(canvasStore.selectedElement.width)"
                @input="canvasStore.updateElement(canvasStore.selectedId, { width: +$event.target.value })"
                type="number"
                class="w-full bg-transparent text-slate-200 outline-none text-xs"
              />
            </div>
            <div class="flex items-center bg-slate-950/60 border border-slate-800 rounded-lg px-2 py-1.5">
              <span class="text-slate-500 font-mono text-[11px] mr-1.5">H:</span>
              <input 
                :value="Math.round(canvasStore.selectedElement.height)"
                @input="canvasStore.updateElement(canvasStore.selectedId, { height: +$event.target.value })"
                type="number"
                class="w-full bg-transparent text-slate-200 outline-none text-xs"
              />
            </div>
          </div>
        </div>

        <!-- Rotation & Flip -->
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="font-semibold text-slate-400">Поворот</label>
            <span class="font-mono text-slate-300">{{ canvasStore.selectedElement.rotation || 0 }}°</span>
          </div>
          <input 
            :value="canvasStore.selectedElement.rotation || 0"
            @input="canvasStore.updateElement(canvasStore.selectedId, { rotation: +$event.target.value })"
            type="range"
            min="0"
            max="360"
            class="w-full accent-blue-500 cursor-pointer"
          />

          <!-- Quick Flip Buttons -->
          <div class="grid grid-cols-2 gap-2 mt-2">
            <button 
              @click="canvasStore.updateElement(canvasStore.selectedId, { flipX: !canvasStore.selectedElement.flipX })"
              :class="canvasStore.selectedElement.flipX ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
              class="py-1.5 px-2 rounded-lg border border-slate-700 text-xs flex items-center justify-center space-x-1.5 transition"
            >
              <FlipHorizontal class="w-3.5 h-3.5" />
              <span>Отразить H</span>
            </button>
            <button 
              @click="canvasStore.updateElement(canvasStore.selectedId, { flipY: !canvasStore.selectedElement.flipY })"
              :class="canvasStore.selectedElement.flipY ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
              class="py-1.5 px-2 rounded-lg border border-slate-700 text-xs flex items-center justify-center space-x-1.5 transition"
            >
              <FlipVertical class="w-3.5 h-3.5" />
              <span>Отразить V</span>
            </button>
          </div>
        </div>

        <!-- Opacity -->
        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="font-semibold text-slate-400">Прозрачность</label>
            <span class="font-mono text-slate-300">
              {{ Math.round((canvasStore.selectedElement.opacity !== undefined ? canvasStore.selectedElement.opacity : 1) * 100) }}%
            </span>
          </div>
          <input 
            :value="canvasStore.selectedElement.opacity !== undefined ? canvasStore.selectedElement.opacity : 1"
            @input="canvasStore.updateElement(canvasStore.selectedId, { opacity: +$event.target.value })"
            type="range"
            min="0.1"
            max="1"
            step="0.05"
            class="w-full accent-blue-500 cursor-pointer"
          />
        </div>

        <!-- Quick actions buttons -->
        <div class="pt-3 border-t border-slate-800 flex space-x-2">
          <button 
            @click="canvasStore.duplicateSelected()"
            class="flex-1 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium flex items-center justify-center space-x-1.5 transition active:scale-95"
          >
            <Copy class="w-3.5 h-3.5 text-blue-400" />
            <span>Дублировать</span>
          </button>
          <button 
            @click="canvasStore.deleteSelected()"
            class="flex-1 py-2 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 text-xs font-medium flex items-center justify-center space-x-1.5 transition active:scale-95"
          >
            <Trash2 class="w-3.5 h-3.5" />
            <span>Удалить</span>
          </button>
        </div>
      </div>

      <!-- Canvas Global Settings (when no object selected) -->
      <div v-else class="space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
          <span class="font-bold text-white text-sm">Параметры холста</span>
          <span class="text-[10px] text-slate-400 font-mono">Общие</span>
        </div>

        <!-- Canvas Dimensions -->
        <div>
          <label class="block font-semibold text-slate-400 mb-1.5">Размеры холста (px)</label>
          <div class="grid grid-cols-2 gap-2">
            <div class="flex items-center bg-slate-950/60 border border-slate-800 rounded-lg px-2 py-1.5">
              <span class="text-slate-500 font-mono text-[11px] mr-1.5">W:</span>
              <input 
                v-model.number="canvasStore.canvasWidth"
                type="number"
                step="50"
                class="w-full bg-transparent text-slate-200 outline-none text-xs"
              />
            </div>
            <div class="flex items-center bg-slate-950/60 border border-slate-800 rounded-lg px-2 py-1.5">
              <span class="text-slate-500 font-mono text-[11px] mr-1.5">H:</span>
              <input 
                v-model.number="canvasStore.canvasHeight"
                type="number"
                step="50"
                class="w-full bg-transparent text-slate-200 outline-none text-xs"
              />
            </div>
          </div>
        </div>

        <!-- Canvas Background Color -->
        <div>
          <label class="block font-semibold text-slate-400 mb-2">Фон холста</label>
          <div class="grid grid-cols-4 gap-2">
            <button 
              v-for="bg in bgPalette" 
              :key="bg.color"
              @click="canvasStore.backgroundColor = bg.color"
              :title="bg.name"
              :class="canvasStore.backgroundColor === bg.color ? 'ring-2 ring-blue-500 scale-105' : 'hover:scale-105'"
              class="h-8 rounded-lg border border-slate-700/60 transition shadow-sm"
              :style="{ backgroundColor: bg.color }"
            ></button>
          </div>
        </div>

        <div class="p-3 rounded-xl bg-slate-950/40 border border-slate-800 text-[11px] text-slate-400 leading-relaxed">
          ℹ️ Выделите любой объект на холсте, чтобы настроить его положение, размер, угол поворота или порядок наложения слоёв.
        </div>
      </div>

    </div>
  </aside>
</template>
