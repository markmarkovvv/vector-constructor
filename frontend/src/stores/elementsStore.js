import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { api } from '../services/api';
import { ISOMETRIC_COMPONENTS } from '../utils/isometricLibrary';

// Pre-packaged Authentic Isometric Figma Design System Components
const BUILT_IN_COMPONENTS = ISOMETRIC_COMPONENTS.map((comp) => ({
  ...comp,
  svg_content: comp.svg_l,
  isBuiltIn: true,
}));

const BUILT_IN_CATEGORIES = Array.from(new Set(BUILT_IN_COMPONENTS.map((c) => c.category)));

export const useElementsStore = defineStore('elements', () => {
  // State
  const illustrations = ref([]);
  const selectedIllustration = ref(null);
  const isLoadingIllustrations = ref(false);

  // Initialize with built-in Figma items so they are immediately available
  const components = ref([...BUILT_IN_COMPONENTS]);
  const categories = ref([...BUILT_IN_CATEGORIES]);
  const activeCategory = ref('all');
  const searchQuery = ref('');
  const isLoadingComponents = ref(false);

  const isInspectorOpen = ref(false);
  const isIllustrationsModalOpen = ref(false);

  // Getters
  const filteredComponents = computed(() => {
    return components.value.filter((item) => {
      const matchCategory =
        activeCategory.value === 'all' || item.category === activeCategory.value;
      const matchSearch =
        !searchQuery.value ||
        item.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        item.category.toLowerCase().includes(searchQuery.value.toLowerCase());
      return matchCategory && matchSearch;
    });
  });

  // Actions
  async function fetchIllustrations() {
    isLoadingIllustrations.value = true;
    try {
      const res = await api.getIllustrations();
      illustrations.value = res.data;
    } catch (err) {
      console.error('Failed to load illustrations:', err);
    } finally {
      isLoadingIllustrations.value = false;
    }
  }

  async function openIllustrationInspector(illustrationId) {
    try {
      const res = await api.getIllustration(illustrationId);
      selectedIllustration.value = res.data;
      isIllustrationsModalOpen.value = false;
      isInspectorOpen.value = true;
    } catch (err) {
      console.error('Failed to load illustration details:', err);
    }
  }

  async function fetchComponents() {
    isLoadingComponents.value = true;
    try {
      const res = await api.getComponents(activeCategory.value, searchQuery.value);
      const apiComps = res.data || [];
      // Combine built-in components and user/extracted components
      const apiIds = new Set(apiComps.map((c) => c.id));
      const remainingBuiltIns = BUILT_IN_COMPONENTS.filter((c) => !apiIds.has(c.id));
      components.value = [...remainingBuiltIns, ...apiComps];

      const mergedCats = new Set([...BUILT_IN_CATEGORIES, ...(res.categories || [])]);
      categories.value = Array.from(mergedCats);
    } catch (err) {
      console.error('Failed to load components:', err);
    } finally {
      isLoadingComponents.value = false;
    }
  }

  async function saveExtractedComponent(payload) {
    try {
      const res = await api.saveComponent(payload);
      components.value.unshift(res.data);
      if (!categories.value.includes(res.data.category)) {
        categories.value.push(res.data.category);
      }
      return res.data;
    } catch (err) {
      console.error('Failed to save component:', err);
      throw err;
    }
  }

  async function deleteComponent(id) {
    const isBuiltIn = components.value.find((c) => c.id === id)?.isBuiltIn;
    if (isBuiltIn) {
      components.value = components.value.filter((c) => c.id !== id);
      return;
    }
    try {
      await api.deleteComponent(id);
      components.value = components.value.filter((c) => c.id !== id);
    } catch (err) {
      console.error('Failed to delete component:', err);
      throw err;
    }
  }

  async function uploadIllustration(payload) {
    try {
      const res = await api.uploadIllustration(payload);
      illustrations.value.unshift(res.data);
      return res.data;
    } catch (err) {
      console.error('Failed to upload illustration:', err);
      throw err;
    }
  }

  return {
    illustrations,
    selectedIllustration,
    isLoadingIllustrations,
    components,
    categories,
    activeCategory,
    searchQuery,
    isLoadingComponents,
    isInspectorOpen,
    isIllustrationsModalOpen,
    filteredComponents,
    fetchIllustrations,
    openIllustrationInspector,
    fetchComponents,
    saveExtractedComponent,
    deleteComponent,
    uploadIllustration,
  };
});
