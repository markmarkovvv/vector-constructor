<script setup>
import { onMounted } from 'vue';
import { useElementsStore } from './stores/elementsStore';
import { useCanvasStore } from './stores/canvasStore';
import HeaderNavbar from './components/HeaderNavbar.vue';
import ElementsSidebar from './components/library/ElementsSidebar.vue';
import VectorCanvas from './components/canvas/VectorCanvas.vue';
import LayersSidebar from './components/canvas/LayersSidebar.vue';
import IllustrationsModal from './components/library/IllustrationsModal.vue';
import SvgInspectorModal from './components/extractor/SvgInspectorModal.vue';
import ExportModal from './components/export/ExportModal.vue';

const elementsStore = useElementsStore();
const canvasStore = useCanvasStore();

onMounted(async () => {
  // Fetch initial catalog data from Laravel API
  await Promise.all([
    elementsStore.fetchIllustrations(),
    elementsStore.fetchComponents(),
  ]);

  // Load the pre-assembled sample composition from backend if available
  try {
    await canvasStore.loadFromBackend(1);
  } catch (err) {
    console.log('No pre-saved composition loaded yet');
  }
});
</script>

<template>
  <div class="h-screen w-screen flex flex-col bg-slate-950 text-slate-100 overflow-hidden font-sans select-none">
    <!-- Top Navigation & Main Controls -->
    <HeaderNavbar />

    <!-- Core App Body: Left Sidebar + Central Artboard + Right Layers & Properties -->
    <main class="flex-1 flex overflow-hidden relative">
      <!-- Left: Library of extracted SVG components -->
      <ElementsSidebar />

      <!-- Center: Vector Canvas Stage -->
      <VectorCanvas />

      <!-- Right: Layers Panel & Transform Properties -->
      <LayersSidebar />
    </main>

    <!-- Modals -->
    <!-- 1. Catalog of 20 SVG Illustrations Modal -->
    <IllustrationsModal />

    <!-- 2. Interactive SVG Element Inspector / Extractor Modal -->
    <SvgInspectorModal />

    <!-- 3. Export to Clean Vector SVG Modal -->
    <ExportModal />
  </div>
</template>

<style>
/* Smooth transitions */
.animate-fade-in {
  animation: fadeIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.98);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
