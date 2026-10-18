import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { api } from '../services/api';

export const useCanvasStore = defineStore('canvas', () => {
  // Canvas configuration
  const canvasWidth = ref(1200);
  const canvasHeight = ref(800);
  const backgroundColor = ref('#ffffff');
  const projectTitle = ref('Новая композиция');
  const compositionId = ref(null);

  // Canvas elements array
  const elements = ref([]);
  const selectedId = ref(null);

  // Viewport / UI
  const zoom = ref(1);
  const snapToGrid = ref(true);
  const gridSize = ref(20);
  const isExportModalOpen = ref(false);

  // History for Undo/Redo
  const history = ref([]);
  const historyIndex = ref(-1);
  const maxHistory = 30;

  // Selected element getter
  const selectedElement = computed(() => {
    return elements.value.find((el) => el.id === selectedId.value) || null;
  });

  // Sorted layers (highest zIndex first for layer panel display)
  const layersSorted = computed(() => {
    return [...elements.value].sort((a, b) => (b.zIndex || 0) - (a.zIndex || 0));
  });

  // Save state for undo/redo
  function recordHistory() {
    // If we were in the middle of history, discard forward history
    if (historyIndex.value < history.value.length - 1) {
      history.value = history.value.slice(0, historyIndex.value + 1);
    }
    // Deep clone elements
    const snapshot = JSON.stringify(elements.value);
    history.value.push(snapshot);
    if (history.value.length > maxHistory) {
      history.value.shift();
    } else {
      historyIndex.value++;
    }
  }

  function undo() {
    if (historyIndex.value > 0) {
      historyIndex.value--;
      elements.value = JSON.parse(history.value[historyIndex.value]);
    }
  }

  function redo() {
    if (historyIndex.value < history.value.length - 1) {
      historyIndex.value++;
      elements.value = JSON.parse(history.value[historyIndex.value]);
    }
  }

  // Add element to canvas from component library
  function addElement(component, dropX = null, dropY = null) {
    const nextZ = elements.value.length > 0 
      ? Math.max(...elements.value.map((e) => e.zIndex || 0)) + 1 
      : 1;

    // Center in visible viewport or drop position
    const w = component.width || 180;
    const h = component.height || 180;
    let x = dropX !== null ? dropX - w / 2 : canvasWidth.value / 2 - w / 2;
    let y = dropY !== null ? dropY - h / 2 : canvasHeight.value / 2 - h / 2;

    if (snapToGrid.value) {
      x = Math.round(x / gridSize.value) * gridSize.value;
      y = Math.round(y / gridSize.value) * gridSize.value;
    }

    const newElement = {
      id: `elem_${Date.now()}_${Math.random().toString(36).substring(2, 7)}`,
      component_id: component.id,
      title: component.title || 'Элемент',
      x: Math.max(0, x),
      y: Math.max(0, y),
      width: w,
      height: h,
      rotation: 0,
      flipX: false,
      flipY: false,
      opacity: 1,
      zIndex: nextZ,
      visible: true,
      locked: false,
      viewBox: component.view_box || '0 0 200 200',
      svgContent: component.svg_content,
      defs: component.defs || '',
    };

    elements.value.push(newElement);
    selectedId.value = newElement.id;
    recordHistory();
  }

  // Select element
  function selectElement(id) {
    selectedId.value = id;
  }

  function clearSelection() {
    selectedId.value = null;
  }

  // Update element transform
  function updateElement(id, updates, commitHistory = true) {
    const idx = elements.value.findIndex((e) => e.id === id);
    if (idx !== -1) {
      elements.value[idx] = { ...elements.value[idx], ...updates };
      if (commitHistory) {
        recordHistory();
      }
    }
  }

  // Delete element
  function deleteElement(id) {
    elements.value = elements.value.filter((e) => e.id !== id);
    if (selectedId.value === id) {
      selectedId.value = null;
    }
    recordHistory();
  }

  function deleteSelected() {
    if (selectedId.value) {
      deleteElement(selectedId.value);
    }
  }

  // Duplicate element
  function duplicateSelected() {
    if (!selectedElement.value) return;
    const base = selectedElement.value;
    const nextZ = Math.max(...elements.value.map((e) => e.zIndex || 0)) + 1;

    const clone = {
      ...JSON.parse(JSON.stringify(base)),
      id: `elem_${Date.now()}_${Math.random().toString(36).substring(2, 7)}`,
      title: `${base.title} (Копия)`,
      x: base.x + 30,
      y: base.y + 30,
      zIndex: nextZ,
    };

    elements.value.push(clone);
    selectedId.value = clone.id;
    recordHistory();
  }

  // Layer order manipulations
  function bringToFront(id) {
    const maxZ = Math.max(...elements.value.map((e) => e.zIndex || 0), 0);
    updateElement(id, { zIndex: maxZ + 1 });
  }

  function sendToBack(id) {
    const minZ = Math.min(...elements.value.map((e) => e.zIndex || 0), 1);
    updateElement(id, { zIndex: Math.max(0, minZ - 1) });
  }

  function bringForward(id) {
    const el = elements.value.find((e) => e.id === id);
    if (!el) return;
    const higherElements = elements.value
      .filter((e) => e.zIndex > el.zIndex)
      .sort((a, b) => a.zIndex - b.zIndex);
    if (higherElements.length > 0) {
      const target = higherElements[0];
      const temp = el.zIndex;
      el.zIndex = target.zIndex;
      target.zIndex = temp;
      recordHistory();
    }
  }

  function sendBackward(id) {
    const el = elements.value.find((e) => e.id === id);
    if (!el) return;
    const lowerElements = elements.value
      .filter((e) => e.zIndex < el.zIndex)
      .sort((a, b) => b.zIndex - a.zIndex);
    if (lowerElements.length > 0) {
      const target = lowerElements[0];
      const temp = el.zIndex;
      el.zIndex = target.zIndex;
      target.zIndex = temp;
      recordHistory();
    }
  }

  function clearCanvas() {
    elements.value = [];
    selectedId.value = null;
    recordHistory();
  }

  // Save composition to backend
  async function saveToBackend() {
    try {
      const payload = {
        title: projectTitle.value,
        canvas_width: canvasWidth.value,
        canvas_height: canvasHeight.value,
        canvas_data: elements.value,
      };

      if (compositionId.value) {
        await api.updateComposition(compositionId.value, payload);
      } else {
        const res = await api.saveComposition(payload);
        compositionId.value = res.data.id;
      }
      return true;
    } catch (err) {
      console.error('Failed to save composition:', err);
      throw err;
    }
  }

  // Load composition from backend
  async function loadFromBackend(id) {
    try {
      const res = await api.getCompositions();
      const comp = res.data.find((c) => c.id === id);
      if (comp) {
        compositionId.value = comp.id;
        projectTitle.value = comp.title;
        canvasWidth.value = comp.canvas_width;
        canvasHeight.value = comp.canvas_height;
        elements.value = comp.canvas_data || [];
        recordHistory();
      }
    } catch (err) {
      console.error('Failed to load composition:', err);
    }
  }

  // Initialize with initial history record
  recordHistory();

  return {
    canvasWidth,
    canvasHeight,
    backgroundColor,
    projectTitle,
    compositionId,
    elements,
    selectedId,
    selectedElement,
    layersSorted,
    zoom,
    snapToGrid,
    gridSize,
    isExportModalOpen,
    recordHistory,
    undo,
    redo,
    addElement,
    selectElement,
    clearSelection,
    updateElement,
    deleteElement,
    deleteSelected,
    duplicateSelected,
    bringToFront,
    sendToBack,
    bringForward,
    sendBackward,
    clearCanvas,
    saveToBackend,
    loadFromBackend,
  };
});
