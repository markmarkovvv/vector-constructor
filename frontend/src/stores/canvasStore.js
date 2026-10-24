import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { api } from '../services/api';
import { COLOR_SCHEMES, DEFAULT_SCHEME_ID, applyColorSchemeToSvg } from '../utils/colorSchemes';
import { CANVAS_FORMATS, DEFAULT_FORMAT_ID } from '../utils/canvasFormats';
import { ISOMETRIC_COMPONENTS } from '../utils/isometricLibrary';

export const useCanvasStore = defineStore('canvas', () => {
  // Format preset (Square 1:1, Horizontal 16:9, Vertical 9:16)
  const currentFormatId = ref(DEFAULT_FORMAT_ID);
  const currentFormat = computed(() => {
    return CANVAS_FORMATS.find((f) => f.id === currentFormatId.value) || CANVAS_FORMATS[0];
  });

  // Canvas dimensions initialized from Square 800x800 preset
  const canvasWidth = ref(800);
  const canvasHeight = ref(800);

  // Color scheme (Lavender, Peach, Mint) from Figma design system
  const currentSchemeId = ref(DEFAULT_SCHEME_ID);
  const currentScheme = computed(() => {
    return COLOR_SCHEMES[currentSchemeId.value] || COLOR_SCHEMES.lavender;
  });
  const backgroundColor = ref(COLOR_SCHEMES.lavender.background);

  // Detail level: 'L' (high detail) | 'S' (simplified)
  const currentDetailLevel = ref('L');

  const projectTitle = ref('Новая композиция');
  const compositionId = ref(null);

  // Canvas elements array
  const elements = ref([]);
  const selectedId = ref(null);

  // Viewport / UI
  const zoom = ref(0.9);
  const snapToGrid = ref(true);
  const gridSize = ref(20);
  const isExportModalOpen = ref(false);
  const isSmartComposerOpen = ref(false);

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
    if (historyIndex.value < history.value.length - 1) {
      history.value = history.value.slice(0, historyIndex.value + 1);
    }
    const snapshot = JSON.stringify({
      elements: elements.value,
      canvasWidth: canvasWidth.value,
      canvasHeight: canvasHeight.value,
      backgroundColor: backgroundColor.value,
      currentSchemeId: currentSchemeId.value,
      currentFormatId: currentFormatId.value,
    });
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
      const state = JSON.parse(history.value[historyIndex.value]);
      elements.value = state.elements || [];
      if (state.canvasWidth) canvasWidth.value = state.canvasWidth;
      if (state.canvasHeight) canvasHeight.value = state.canvasHeight;
      if (state.backgroundColor) backgroundColor.value = state.backgroundColor;
      if (state.currentSchemeId) currentSchemeId.value = state.currentSchemeId;
      if (state.currentFormatId) currentFormatId.value = state.currentFormatId;
    }
  }

  function redo() {
    if (historyIndex.value < history.value.length - 1) {
      historyIndex.value++;
      const state = JSON.parse(history.value[historyIndex.value]);
      elements.value = state.elements || [];
      if (state.canvasWidth) canvasWidth.value = state.canvasWidth;
      if (state.canvasHeight) canvasHeight.value = state.canvasHeight;
      if (state.backgroundColor) backgroundColor.value = state.backgroundColor;
      if (state.currentSchemeId) currentSchemeId.value = state.currentSchemeId;
      if (state.currentFormatId) currentFormatId.value = state.currentFormatId;
    }
  }

  // Set canvas format preset (Square, Horizontal, Vertical)
  function setFormat(formatId) {
    const fmt = CANVAS_FORMATS.find((f) => f.id === formatId);
    if (!fmt) return;
    currentFormatId.value = formatId;
    canvasWidth.value = fmt.width;
    canvasHeight.value = fmt.height;

    // Adjust zoom so entire artboard comfortably fits on screen
    if (fmt.id === 'horizontal') {
      zoom.value = 0.75;
    } else if (fmt.id === 'vertical') {
      zoom.value = 0.7;
    } else {
      zoom.value = 0.85;
    }
    recordHistory();
  }

  // Set official color scheme (Lavender, Peach, Mint)
  function setColorScheme(schemeId) {
    const scheme = COLOR_SCHEMES[schemeId];
    if (!scheme) return;
    currentSchemeId.value = schemeId;
    backgroundColor.value = scheme.background;
    recordHistory();
  }

  // Set global detail level (L or S) and update canvas elements if they have L/S versions
  function setDetailLevel(level) {
    currentDetailLevel.value = level;
    elements.value.forEach((el) => {
      const iso = ISOMETRIC_COMPONENTS.find((item) => item.id === el.component_id);
      if (iso) {
        el.detailLevel = level;
        el.svgContent = level === 'S' ? iso.svg_s : iso.svg_l;
        el.viewBox = level === 'S' ? (iso.view_box_s || iso.view_box) : (iso.view_box_l || iso.view_box);
      }
    });
    recordHistory();
  }

  // Toggle single element state between Main (Color) and Background (Monochrome)
  function toggleElementState(id) {
    const el = elements.value.find((e) => e.id === id);
    if (el) {
      el.isBackground = !el.isBackground;
      recordHistory();
    }
  }

  // Toggle single element detail level between L and S
  function toggleElementDetail(id) {
    const el = elements.value.find((e) => e.id === id);
    if (!el) return;
    const iso = ISOMETRIC_COMPONENTS.find((item) => item.id === el.component_id);
    if (iso) {
      const nextLevel = el.detailLevel === 'S' ? 'L' : 'S';
      el.detailLevel = nextLevel;
      el.svgContent = nextLevel === 'S' ? iso.svg_s : iso.svg_l;
      el.viewBox = nextLevel === 'S' ? (iso.view_box_s || iso.view_box) : (iso.view_box_l || iso.view_box);
      recordHistory();
    }
  }

  // Add element to canvas from component library
  function addElement(component, dropX = null, dropY = null, isBackground = false) {
    const nextZ = elements.value.length > 0 
      ? Math.max(...elements.value.map((e) => e.zIndex || 0)) + 1 
      : 1;

    const w = component.width || 180;
    const h = component.height || 180;
    let x = dropX !== null ? dropX - w / 2 : canvasWidth.value / 2 - w / 2;
    let y = dropY !== null ? dropY - h / 2 : canvasHeight.value / 2 - h / 2;

    if (snapToGrid.value) {
      x = Math.round(x / gridSize.value) * gridSize.value;
      y = Math.round(y / gridSize.value) * gridSize.value;
    }

    // Determine initial svgContent and viewBox based on detail level
    let content = component.svg_content;
    let vb = component.view_box || '0 0 200 200';
    if (component.svg_l && component.svg_s) {
      const isS = currentDetailLevel.value === 'S';
      content = isS ? component.svg_s : component.svg_l;
      vb = isS ? (component.view_box_s || vb) : (component.view_box_l || vb);
    }

    const newElement = {
      id: `elem_${Date.now()}_${Math.random().toString(36).substring(2, 7)}`,
      component_id: component.id,
      title: component.title || 'Элемент',
      isBackground: isBackground || component.isBackground || false,
      detailLevel: currentDetailLevel.value,
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
      viewBox: vb,
      svgContent: content,
      defs: component.defs || '',
    };

    elements.value.push(newElement);
    selectedId.value = newElement.id;
    recordHistory();
  }

  // Apply automated composition result from SmartComposer
  function applyAutoComposition(result) {
    if (!result || !result.elements) return;

    if (result.canvasWidth && result.canvasHeight) {
      canvasWidth.value = result.canvasWidth;
      canvasHeight.value = result.canvasHeight;
    }
    if (result.formatId) {
      currentFormatId.value = result.formatId;
    }
    if (result.schemeId) {
      setColorScheme(result.schemeId);
    }

    elements.value = result.elements;
    selectedId.value = null;

    // Adjust zoom
    if (currentFormatId.value === 'horizontal') zoom.value = 0.75;
    else if (currentFormatId.value === 'vertical') zoom.value = 0.7;
    else zoom.value = 0.85;

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

  // Initial history record
  recordHistory();

  return {
    currentFormatId,
    currentFormat,
    canvasWidth,
    canvasHeight,
    currentSchemeId,
    currentScheme,
    backgroundColor,
    currentDetailLevel,
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
    isSmartComposerOpen,
    recordHistory,
    undo,
    redo,
    setFormat,
    setColorScheme,
    setDetailLevel,
    toggleElementState,
    toggleElementDetail,
    addElement,
    applyAutoComposition,
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
