<script setup>
import { ref, computed } from 'vue';
import { useElementsStore } from '../../stores/elementsStore';
import { 
  X, 
  Search, 
  Sparkles, 
  Scissors, 
  CheckCircle2, 
  ArrowRight, 
  Upload, 
  Plus, 
  FileCode, 
  Check
} from 'lucide-vue-next';

const elementsStore = useElementsStore();
const searchQuery = ref('');
const selectedCategory = ref('all');
const fileInputRef = ref(null);
const isUploading = ref(false);
const isDraggingFile = ref(false);
const uploadSuccessMessage = ref('');

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

function triggerFileInput() {
  if (fileInputRef.value) {
    fileInputRef.value.click();
  }
}

async function handleFileChange(e) {
  const file = e.target.files?.[0];
  if (!file) return;
  await processSvgFile(file);
  e.target.value = ''; // reset file input
}

function handleDragOver(e) {
  e.preventDefault();
  isDraggingFile.value = true;
}

function handleDragLeave(e) {
  e.preventDefault();
  isDraggingFile.value = false;
}

async function handleDrop(e) {
  e.preventDefault();
  isDraggingFile.value = false;
  const file = e.dataTransfer?.files?.[0];
  if (file) {
    await processSvgFile(file);
  }
}

async function processSvgFile(file) {
  if (!file.name.toLowerCase().endsWith('.svg') && file.type !== 'image/svg+xml') {
    alert('Пожалуйста, выберите файл в формате .SVG');
    return;
  }

  isUploading.value = true;
  try {
    const text = await file.text();
    if (!text.includes('<svg')) {
      alert('Выбранный файл не содержит валидной разметки SVG');
      return;
    }

    // Clean up title from filename: e.g. "my_isometric-city.svg" -> "My Isometric City"
    const rawName = file.name.replace(/\.svg$/i, '');
    const cleanTitle = rawName
      .replace(/[_-]/g, ' ')
      .replace(/\s+/g, ' ')
      .trim()
      .replace(/^\w/, (c) => c.toUpperCase());

    // Extract viewBox if exists
    let viewBox = '0 0 800 600';
    const vbMatch = text.match(/viewBox\s*=\s*["']([^"']+)["']/i);
    if (vbMatch) {
      viewBox = vbMatch[1];
    }

    const newIllustration = await elementsStore.uploadIllustration({
      title: cleanTitle || 'Загруженная иллюстрация',
      category: 'Пользовательские',
      svg_content: text,
      view_box: viewBox,
      description: `Пользовательский файл ${file.name} (${Math.round(file.size / 1024)} КБ)`,
    });

    uploadSuccessMessage.value = `Иллюстрация «${cleanTitle}» загружена!`;
    setTimeout(() => {
      uploadSuccessMessage.value = '';
    }, 3000);

    // Switch category filter to "Пользовательские" or "all" to show the new item
    selectedCategory.value = 'all';

    // Immediately open the inspector for this newly uploaded illustration!
    elementsStore.openIllustrationInspector(newIllustration.id);
  } catch (err) {
    console.error('Ошибка загрузки SVG:', err);
    alert('Произошла ошибка при загрузке SVG на сервер');
  } finally {
    isUploading.value = false;
  }
}
</script>

<template>
  <div 
    v-if="elementsStore.isIllustrationsModalOpen" 
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/85 backdrop-blur-md animate-fade-in select-none"
  >
    <!-- Modal Card (fixed height 90vh with flex column layout) -->
    <div 
      @dragover="handleDragOver"
      @dragleave="handleDragLeave"
      @drop="handleDrop"
      class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-6xl h-[90vh] flex flex-col shadow-2xl overflow-hidden relative"
    >
      <!-- Drag & Drop overlay indicator -->
      <div 
        v-if="isDraggingFile"
        class="absolute inset-0 z-50 bg-blue-600/90 backdrop-blur-sm border-4 border-dashed border-white rounded-2xl flex flex-col items-center justify-center text-white space-y-3 pointer-events-none"
      >
        <Upload class="w-16 h-16 animate-bounce" />
        <h3 class="text-xl font-bold">Отпустите SVG-файл здесь</h3>
        <p class="text-sm opacity-80">Файл сразу загрузится в каталог и откроется в инспекторе элементов</p>
      </div>

      <!-- Hidden file input for SVG upload -->
      <input 
        ref="fileInputRef" 
        type="file" 
        accept=".svg,image/svg+xml" 
        class="hidden" 
        @change="handleFileChange" 
      />

      <!-- 1. Modal Header (shrink-0) -->
      <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/90 shrink-0">
        <div class="flex items-center space-x-3">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500/20 to-orange-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 shadow-sm">
            <Sparkles class="w-5 h-5" />
          </div>
          <div>
            <div class="flex items-center space-x-2">
              <h2 class="text-base font-bold text-white tracking-wide">
                Каталог векторных иллюстраций
              </h2>
              <span class="text-[11px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 font-semibold">
                {{ elementsStore.illustrations.length }} иллюстраций
              </span>
            </div>
            <p class="text-xs text-slate-400">
              Выберите любую иллюстрацию или загрузите свой SVG, чтобы извлечь объекты в библиотеку компонентов
            </p>
          </div>
        </div>

        <div class="flex items-center space-x-2">
          <!-- Prominent "Upload own SVG" Button -->
          <button 
            @click="triggerFileInput"
            :disabled="isUploading"
            class="flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-semibold shadow-md shadow-emerald-500/20 transition active:scale-95 cursor-pointer disabled:opacity-50"
          >
            <Upload class="w-4 h-4" />
            <span>{{ isUploading ? 'Загрузка...' : '+ Загрузить свой SVG' }}</span>
          </button>

          <button 
            @click="elementsStore.isIllustrationsModalOpen = false"
            class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition"
          >
            <X class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Success Notification banner if uploaded -->
      <div 
        v-if="uploadSuccessMessage"
        class="bg-emerald-500/20 border-b border-emerald-500/30 px-6 py-2 text-xs text-emerald-300 flex items-center space-x-2 shrink-0 animate-fade-in"
      >
        <Check class="w-4 h-4 text-emerald-400" />
        <span>{{ uploadSuccessMessage }} Открываем инспектор компонентов...</span>
      </div>

      <!-- 2. Filters & Search Toolbar (shrink-0) -->
      <div class="px-6 py-3 border-b border-slate-800/80 bg-slate-950/50 flex flex-wrap items-center justify-between gap-3 shrink-0">
        <!-- Search bar -->
        <div class="relative w-72">
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Поиск по названию или описанию..."
            class="w-full bg-slate-800/90 text-xs text-slate-100 placeholder-slate-400 pl-9 pr-3 py-2 rounded-xl border border-slate-700/60 focus:border-blue-500 focus:outline-none transition"
          />
        </div>

        <!-- Category pills with scroll -->
        <div class="flex items-center space-x-2 overflow-x-auto pb-1 max-w-full">
          <button 
            v-for="cat in categories" 
            :key="cat"
            @click="selectedCategory = cat"
            :class="selectedCategory === cat ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25' : 'bg-slate-800/90 text-slate-300 hover:bg-slate-700 border border-slate-700/50'"
            class="text-xs px-3 py-1.5 rounded-xl font-medium transition whitespace-nowrap active:scale-95 cursor-pointer"
          >
            {{ cat === 'all' ? `Все иллюстрации (${elementsStore.illustrations.length})` : cat }}
          </button>
        </div>
      </div>

      <!-- 3. SCROLLABLE CONTAINER: flex-1 + min-h-0 + overflow-y-auto guarantees proper scrolling! -->
      <div class="flex-1 min-h-0 overflow-y-auto p-6">
        <!-- Independent Grid inside the scroll container -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
          
          <!-- Upload Drop Card (First item as quick upload prompt) -->
          <div 
            @click="triggerFileInput"
            class="border-2 border-dashed border-slate-700/80 hover:border-emerald-500/60 bg-slate-950/40 hover:bg-emerald-950/10 rounded-2xl flex flex-col items-center justify-center p-6 text-center transition-all cursor-pointer group min-h-[340px]"
          >
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 group-hover:scale-110 flex items-center justify-center text-emerald-400 mb-4 transition-transform shadow-lg shadow-emerald-500/5">
              <Upload class="w-7 h-7" />
            </div>
            <h3 class="text-sm font-bold text-slate-200 group-hover:text-emerald-400 transition mb-1">
              Загрузить свой SVG
            </h3>
            <p class="text-xs text-slate-400 max-w-[200px] leading-relaxed mb-4">
              Кликните для выбора файла или просто перетащите .svg сюда
            </p>
            <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-slate-800 group-hover:bg-emerald-600 group-hover:text-white text-slate-300 transition flex items-center space-x-1.5 border border-slate-700">
              <Plus class="w-3.5 h-3.5" />
              <span>Выбрать файл</span>
            </span>
          </div>

          <!-- All Illustration Cards -->
          <div 
            v-for="item in filteredIllustrations" 
            :key="item.id"
            @click="handleOpenInspector(item.id)"
            class="bg-slate-950/80 border border-slate-800/90 hover:border-blue-500/60 rounded-2xl overflow-hidden transition-all duration-200 hover:shadow-2xl hover:shadow-blue-500/10 group flex flex-col cursor-pointer min-h-[340px]"
          >
            <!-- SVG Thumbnail Box (shrink-0 and h-44 prevents squishing) -->
            <div class="h-44 shrink-0 bg-slate-900/80 relative flex items-center justify-center p-3 border-b border-slate-800/80 group-hover:bg-slate-900 transition overflow-hidden">
              <!-- Render live SVG -->
              <div 
                class="w-full h-full flex items-center justify-center pointer-events-none svg-thumb-container"
                v-html="item.svg_content"
              ></div>
              
              <!-- Badges -->
              <div class="absolute top-2.5 left-2.5">
                <span 
                  :class="item.category === 'Пользовательские' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-slate-950/85 text-blue-400 border-slate-800'"
                  class="text-[11px] font-bold font-mono px-2 py-0.5 rounded-lg border backdrop-blur-xs"
                >
                  {{ item.category === 'Пользовательские' ? 'Свой SVG' : `#${item.id}` }}
                </span>
              </div>

              <div class="absolute top-2.5 right-2.5 flex items-center space-x-1.5">
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-lg bg-slate-950/85 text-emerald-400 border border-slate-800 backdrop-blur-xs flex items-center space-x-1">
                  <CheckCircle2 class="w-3 h-3 text-emerald-400" />
                  <span>{{ item.components_count || 0 }} готово</span>
                </span>
              </div>

              <!-- Hover overlay CTA -->
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
                <span class="text-xs font-semibold text-blue-400 group-hover:text-blue-300 flex items-center space-x-1">
                  <span>Инспектор объектов</span>
                  <ArrowRight class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                </span>
                <span class="text-[10px] text-slate-400 font-mono">
                  {{ item.view_box ? item.view_box.split(' ').slice(2).join('×') : '800×600' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty Search Result -->
        <div v-if="filteredIllustrations.length === 0" class="text-center py-16 text-slate-400">
          <Sparkles class="w-10 h-10 mx-auto mb-2 opacity-30 text-slate-400" />
          <p class="text-sm font-semibold text-slate-300">Иллюстрации не найдены</p>
          <p class="text-xs text-slate-400 mt-1">Попробуйте изменить поисковый запрос</p>
        </div>
      </div>

      <!-- 4. Modal Footer (shrink-0) -->
      <div class="px-6 py-3.5 border-t border-slate-800 bg-slate-950/70 flex items-center justify-between text-xs text-slate-400 shrink-0">
        <span class="flex items-center space-x-1.5">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span>В каталоге: <strong class="text-slate-200">{{ elementsStore.illustrations.length }}</strong> векторных иллюстраций</span>
        </span>
        <span class="text-slate-400">Поддерживается перетаскивание любых .svg файлов прямо в это окно</span>
      </div>

    </div>
  </div>
</template>
