const API_BASE = 'http://127.0.0.1:8000/api';

export const api = {
  // Illustrations (Исходные 20 иллюстраций)
  async getIllustrations() {
    const res = await fetch(`${API_BASE}/illustrations`);
    if (!res.ok) throw new Error('Ошибка загрузки иллюстраций');
    return res.json();
  },

  async getIllustration(id) {
    const res = await fetch(`${API_BASE}/illustrations/${id}`);
    if (!res.ok) throw new Error(`Ошибка загрузки иллюстрации #${id}`);
    return res.json();
  },

  async uploadIllustration(data) {
    const res = await fetch(`${API_BASE}/illustrations`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });
    if (!res.ok) throw new Error('Ошибка сохранения иллюстрации');
    return res.json();
  },

  // Components (Библиотека извлечённых элементов)
  async getComponents(category = 'all', search = '') {
    const params = new URLSearchParams();
    if (category && category !== 'all') params.append('category', category);
    if (search) params.append('search', search);

    const res = await fetch(`${API_BASE}/components?${params.toString()}`);
    if (!res.ok) throw new Error('Ошибка загрузки компонентов');
    return res.json();
  },

  async saveComponent(payload) {
    const res = await fetch(`${API_BASE}/components`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    if (!res.ok) throw new Error('Ошибка сохранения компонента в библиотеку');
    return res.json();
  },

  async deleteComponent(id) {
    const res = await fetch(`${API_BASE}/components/${id}`, {
      method: 'DELETE',
    });
    if (!res.ok) throw new Error('Ошибка удаления компонента');
    return res.json();
  },

  // Compositions (Сохраненные проекты)
  async getCompositions() {
    const res = await fetch(`${API_BASE}/compositions`);
    if (!res.ok) throw new Error('Ошибка загрузки проектов');
    return res.json();
  },

  async saveComposition(payload) {
    const res = await fetch(`${API_BASE}/compositions`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    if (!res.ok) throw new Error('Ошибка сохранения проекта');
    return res.json();
  },

  async updateComposition(id, payload) {
    const res = await fetch(`${API_BASE}/compositions/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    if (!res.ok) throw new Error('Ошибка обновления проекта');
    return res.json();
  },

  async deleteComposition(id) {
    const res = await fetch(`${API_BASE}/compositions/${id}`, {
      method: 'DELETE',
    });
    if (!res.ok) throw new Error('Ошибка удаления проекта');
    return res.json();
  },
};
