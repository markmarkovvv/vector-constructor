/**
 * Compiles canvas layers into a valid, standalone, scalable SVG vector document.
 * Adheres strictly to the vector constraint (100% pure SVG paths, no rasterization).
 */

import { applyColorSchemeToSvg } from './colorSchemes';

export function compileToSvg(elements, options = {}) {
  const {
    width = 800,
    height = 800,
    backgroundColor = '#C7C5FF',
    schemeId = 'lavender',
    includeBackground = true,
    title = 'Vector Composition',
  } = options;

  // Sort elements by zIndex ascending
  const sorted = [...elements].sort((a, b) => (a.zIndex || 0) - (b.zIndex || 0));

  // Collect all defs without duplicates
  const defsSet = new Set();
  sorted.forEach((item) => {
    if (item.defs) {
      defsSet.add(item.defs.trim());
    }
  });
  const defsString = Array.from(defsSet).join('\n');

  // Build layers SVG
  const layersSvg = sorted
    .filter((item) => item.visible !== false)
    .map((item) => {
      const cx = item.x + item.width / 2;
      const cy = item.y + item.height / 2;
      const rot = item.rotation || 0;
      const opacity = item.opacity !== undefined ? item.opacity : 1;

      // Transform calculation
      let transformStr = `translate(${item.x}, ${item.y})`;
      if (rot !== 0) {
        transformStr = `translate(${cx}, ${cy}) rotate(${rot}) translate(${-item.width / 2}, ${-item.height / 2})`;
      }
      if (item.flipX || item.flipY) {
        const sx = item.flipX ? -1 : 1;
        const sy = item.flipY ? -1 : 1;
        transformStr += ` scale(${sx}, ${sy})`;
      }

      // Apply color scheme and monochrome state (Main vs Background)
      const coloredContent = applyColorSchemeToSvg(item.svgContent, schemeId, item.isBackground);

      return `  <!-- Layer: ${escapeXml(item.title || 'Element')} [${item.isBackground ? 'Фоновый' : 'Главный'}] -->
  <g transform="${transformStr}" opacity="${opacity}">
    <svg width="${item.width}" height="${item.height}" viewBox="${item.viewBox || `0 0 ${item.width} ${item.height}`}" preserveAspectRatio="xMidYMid meet">
      ${coloredContent}
    </svg>
  </g>`;
    })
    .join('\n\n');

  // Standalone SVG template
  return `<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" 
     xmlns:xlink="http://www.w3.org/1999/xlink" 
     viewBox="0 0 ${width} ${height}" 
     width="${width}" 
     height="${height}">
  <title>${escapeXml(title)}</title>
  <desc>Created with Vector Constructor (MVP Figma Design System)</desc>
  ${defsString ? `<defs>\n${defsString}\n  </defs>` : ''}
  ${includeBackground ? `<rect width="${width}" height="${height}" fill="${backgroundColor}" />` : ''}

${layersSvg}
</svg>`;
}

function escapeXml(unsafe) {
  return String(unsafe).replace(/[<>&'"]/g, (c) => {
    switch (c) {
      case '<': return '&lt;';
      case '>': return '&gt;';
      case '&': return '&amp;';
      case '\'': return '&apos;';
      case '"': return '&quot;';
    }
  });
}

/**
 * Trigger browser file download for SVG text content
 */
export function downloadSvgFile(svgContent, filename = 'composition.svg') {
  const blob = new Blob([svgContent], { type: 'image/svg+xml;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}
