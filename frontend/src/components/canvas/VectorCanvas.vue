<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useCanvasStore } from '../../stores/canvasStore';

const canvasStore = useCanvasStore();
const canvasBoardRef = ref(null);

// Transform / Drag interaction state
const isDragging = ref(false);
const isResizing = ref(false);
const isRotating = ref(false);
const resizeHandle = ref('');

let startMouseX = 0;
let startMouseY = 0;
let startElemX = 0;
let startElemY = 0;
let startWidth = 0;
let startHeight = 0;
let startRotation = 0;
let elemCenterX = 0;
let elemCenterY = 0;

// Drop component from sidebar
function handleDrop(e) {
  e.preventDefault();
  const rawData = e.dataTransfer.getData('application/json');
  if (!rawData || !canvasBoardRef.value) return;

  try {
    const comp = JSON.parse(rawData);
    const rect = canvasBoardRef.value.getBoundingClientRect();
    const dropX = (e.clientX - rect.left) / canvasStore.zoom;
    const dropY = (e.clientY - rect.top) / canvasStore.zoom;
    canvasStore.addElement(comp, dropX, dropY);
  } catch (err) {
    console.error('Failed to parse dropped element:', err);
  }
}

function handleDragOver(e) {
  e.preventDefault();
  e.dataTransfer.dropEffect = 'copy';
}

// Canvas click to deselect
function handleCanvasMouseDown(e) {
  if (e.target === canvasBoardRef.value || e.target.classList.contains('canvas-board-bg')) {
    canvasStore.clearSelection();
  }
}

// Element pointer down -> start move
function handleElementMouseDown(e, item) {
  if (item.locked) return;
  e.stopPropagation();
  canvasStore.selectElement(item.id);

  isDragging.value = true;
  startMouseX = e.clientX;
  startMouseY = e.clientY;
  startElemX = item.x;
  startElemY = item.y;

  window.addEventListener('mousemove', handleElementMouseMove);
  window.addEventListener('mouseup', handleElementMouseUp);
}

function handleElementMouseMove(e) {
  if (!isDragging.value || !canvasStore.selectedElement) return;

  const dx = (e.clientX - startMouseX) / canvasStore.zoom;
  const dy = (e.clientY - startMouseY) / canvasStore.zoom;

  let newX = startElemX + dx;
  let newY = startElemY + dy;

  if (canvasStore.snapToGrid) {
    newX = Math.round(newX / canvasStore.gridSize) * canvasStore.gridSize;
    newY = Math.round(newY / canvasStore.gridSize) * canvasStore.gridSize;
  }

  canvasStore.updateElement(canvasStore.selectedId, { x: newX, y: newY }, false);
}

function handleElementMouseUp() {
  if (isDragging.value) {
    isDragging.value = false;
    canvasStore.recordHistory();
  }
  window.removeEventListener('mousemove', handleElementMouseMove);
  window.removeEventListener('mouseup', handleElementMouseUp);
}

// Resize handle mouse down
function handleResizeMouseDown(e, handle) {
  e.stopPropagation();
  if (!canvasStore.selectedElement) return;

  isResizing.value = true;
  resizeHandle.value = handle;
  startMouseX = e.clientX;
  startMouseY = e.clientY;

  const el = canvasStore.selectedElement;
  startElemX = el.x;
  startElemY = el.y;
  startWidth = el.width;
  startHeight = el.height;

  window.addEventListener('mousemove', handleResizeMouseMove);
  window.addEventListener('mouseup', handleResizeMouseUp);
}

function handleResizeMouseMove(e) {
  if (!isResizing.value || !canvasStore.selectedElement) return;

  const dx = (e.clientX - startMouseX) / canvasStore.zoom;
  const dy = (e.clientY - startMouseY) / canvasStore.zoom;
  const aspectRatio = startWidth / startHeight;

  let newW = startWidth;
  let newH = startHeight;
  let newX = startElemX;
  let newY = startElemY;

  if (resizeHandle.value === 'se') {
    newW = Math.max(20, startWidth + dx);
    newH = newW / aspectRatio;
  } else if (resizeHandle.value === 'sw') {
    newW = Math.max(20, startWidth - dx);
    newH = newW / aspectRatio;
    newX = startElemX + (startWidth - newW);
  } else if (resizeHandle.value === 'ne') {
    newW = Math.max(20, startWidth + dx);
    newH = newW / aspectRatio;
    newY = startElemY + (startHeight - newH);
  } else if (resizeHandle.value === 'nw') {
    newW = Math.max(20, startWidth - dx);
    newH = newW / aspectRatio;
    newX = startElemX + (startWidth - newW);
    newY = startElemY + (startHeight - newH);
  }

  canvasStore.updateElement(
    canvasStore.selectedId,
    { x: Math.round(newX), y: Math.round(newY), width: Math.round(newW), height: Math.round(newH) },
    false
  );
}

function handleResizeMouseUp() {
  if (isResizing.value) {
    isResizing.value = false;
    canvasStore.recordHistory();
  }
  window.removeEventListener('mousemove', handleResizeMouseMove);
  window.removeEventListener('mouseup', handleResizeMouseUp);
}

// Rotation handle mouse down
function handleRotateMouseDown(e) {
  e.stopPropagation();
  if (!canvasStore.selectedElement) return;

  isRotating.value = true;
  const el = canvasStore.selectedElement;
  const rect = canvasBoardRef.value.getBoundingClientRect();
  elemCenterX = rect.left + (el.x + el.width / 2) * canvasStore.zoom;
  elemCenterY = rect.top + (el.y + el.height / 2) * canvasStore.zoom;

  const initialAngle = Math.atan2(e.clientY - elemCenterY, e.clientX - elemCenterX);
  startRotation = el.rotation - (initialAngle * 180) / Math.PI;

  window.addEventListener('mousemove', handleRotateMouseMove);
  window.addEventListener('mouseup', handleRotateMouseUp);
}

function handleRotateMouseMove(e) {
  if (!isRotating.value || !canvasStore.selectedElement) return;

  const currentAngle = Math.atan2(e.clientY - elemCenterY, e.clientX - elemCenterX);
  let degree = (currentAngle * 180) / Math.PI + startRotation;

  // Snap to 45 degree increments if shift pressed
  if (e.shiftKey) {
    degree = Math.round(degree / 45) * 45;
  }

  // Normalize degree 0..360
  degree = Math.round((degree % 360 + 360) % 360);

  canvasStore.updateElement(canvasStore.selectedId, { rotation: degree }, false);
}

function handleRotateMouseUp() {
  if (isRotating.value) {
    isRotating.value = false;
    canvasStore.recordHistory();
  }
  window.removeEventListener('mousemove', handleRotateMouseMove);
  window.removeEventListener('mouseup', handleRotateMouseUp);
}

// Global Keyboard shortcuts
function handleKeyDown(e) {
  // Ignore if user typing in input/textarea
  if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) return;

  if (e.key === 'Delete' || e.key === 'Backspace') {
    if (canvasStore.selectedId) {
      e.preventDefault();
      canvasStore.deleteSelected();
    }
  } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
    e.preventDefault();
    if (e.shiftKey) {
      canvasStore.redo();
    } else {
      canvasStore.undo();
    }
  } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'y') {
    e.preventDefault();
    canvasStore.redo();
  } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'd') {
    e.preventDefault();
    canvasStore.duplicateSelected();
  } else if (canvasStore.selectedElement) {
    const step = e.shiftKey ? canvasStore.gridSize : 2;
    const el = canvasStore.selectedElement;
    if (e.key === 'ArrowLeft') {
      e.preventDefault();
      canvasStore.updateElement(el.id, { x: el.x - step });
    } else if (e.key === 'ArrowRight') {
      e.preventDefault();
      canvasStore.updateElement(el.id, { x: el.x + step });
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      canvasStore.updateElement(el.id, { y: el.y - step });
    } else if (e.key === 'ArrowDown') {
      e.preventDefault();
      canvasStore.updateElement(el.id, { y: el.y + step });
    }
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
  <div 
    class="flex-1 h-full canvas-grid-pattern overflow-auto flex items-center justify-center p-8 relative"
    @mousedown="handleCanvasMouseDown"
  >
    <!-- Canvas Artboard -->
    <div 
      ref="canvasBoardRef"
      @dragover="handleDragOver"
      @drop="handleDrop"
      class="canvas-board-bg relative shadow-2xl transition-transform origin-center select-none"
      :style="{
        width: `${canvasStore.canvasWidth}px`,
        height: `${canvasStore.canvasHeight}px`,
        transform: `scale(${canvasStore.zoom})`,
        backgroundColor: canvasStore.backgroundColor,
      }"
    >
      <!-- Optional Grid Overlay -->
      <div 
        v-if="canvasStore.snapToGrid" 
        class="absolute inset-0 pointer-events-none opacity-20"
        :style="{
          backgroundImage: `linear-gradient(to right, #94a3b8 1px, transparent 1px), linear-gradient(to bottom, #94a3b8 1px, transparent 1px)`,
          backgroundSize: `${canvasStore.gridSize}px ${canvasStore.gridSize}px`
        }"
      ></div>

      <!-- Render all Vector Elements -->
      <div 
        v-for="item in canvasStore.elements" 
        :key="item.id"
        v-show="item.visible !== false"
        @mousedown="handleElementMouseDown($event, item)"
        class="absolute group cursor-move select-none"
        :style="{
          left: `${item.x}px`,
          top: `${item.y}px`,
          width: `${item.width}px`,
          height: `${item.height}px`,
          zIndex: item.zIndex,
          transform: `rotate(${item.rotation || 0}deg) scale(${item.flipX ? -1 : 1}, ${item.flipY ? -1 : 1})`,
          opacity: item.opacity !== undefined ? item.opacity : 1,
        }"
      >
        <!-- The pure vector SVG content -->
        <svg 
          :viewBox="item.viewBox" 
          width="100%" 
          height="100%" 
          preserveAspectRatio="xMidYMid meet"
          class="w-full h-full pointer-events-none overflow-visible"
          v-html="item.svgContent"
        ></svg>

        <!-- Selection Box with Handles (active when selected) -->
        <div 
          v-if="canvasStore.selectedId === item.id" 
          class="absolute inset-0 border-2 border-blue-500 pointer-events-none shadow-sm shadow-blue-500/30"
        >
          <!-- Center label -->
          <div class="absolute -top-6 left-0 bg-blue-600 text-white font-mono text-[10px] px-1.5 py-0.5 rounded shadow whitespace-nowrap pointer-events-none">
            {{ item.title }} · {{ Math.round(item.width) }}×{{ Math.round(item.height) }}
          </div>

          <!-- Rotation Handle (top line + circle) -->
          <div 
            @mousedown="handleRotateMouseDown"
            class="absolute -top-7 left-1/2 -translate-x-1/2 w-4 h-4 bg-white border-2 border-blue-600 rounded-full cursor-grab active:cursor-grabbing pointer-events-auto shadow hover:scale-125 transition"
            title="Вращение"
          ></div>
          <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 w-0.5 h-3.5 bg-blue-500 pointer-events-none"></div>

          <!-- 4 Corner Resize Handles -->
          <div 
            @mousedown="handleResizeMouseDown($event, 'nw')" 
            class="absolute -top-1.5 -left-1.5 w-3 h-3 bg-white border-2 border-blue-600 rounded-sm cursor-nwse-resize pointer-events-auto hover:scale-125 transition"
          ></div>
          <div 
            @mousedown="handleResizeMouseDown($event, 'ne')" 
            class="absolute -top-1.5 -right-1.5 w-3 h-3 bg-white border-2 border-blue-600 rounded-sm cursor-nesw-resize pointer-events-auto hover:scale-125 transition"
          ></div>
          <div 
            @mousedown="handleResizeMouseDown($event, 'se')" 
            class="absolute -bottom-1.5 -right-1.5 w-3 h-3 bg-white border-2 border-blue-600 rounded-sm cursor-nwse-resize pointer-events-auto hover:scale-125 transition"
          ></div>
          <div 
            @mousedown="handleResizeMouseDown($event, 'sw')" 
            class="absolute -bottom-1.5 -left-1.5 w-3 h-3 bg-white border-2 border-blue-600 rounded-sm cursor-nesw-resize pointer-events-auto hover:scale-125 transition"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>
