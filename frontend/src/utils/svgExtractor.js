/**
 * Utility for parsing and isolating SVG elements/groups into reusable components.
 */

export function extractNodeData(element, rootSvg) {
  if (!element || !rootSvg) return null;

  try {
    // 1. Get bounding box of the element within SVG coordinate system
    let bbox;
    if (typeof element.getBBox === 'function') {
      bbox = element.getBBox();
    } else {
      // Fallback if not directly on screen
      bbox = { x: 0, y: 0, width: 200, height: 200 };
    }

    // Add comfortable padding around the component
    const pad = Math.max(6, Math.min(bbox.width, bbox.height) * 0.05);
    const minX = Math.round(bbox.x - pad);
    const minY = Math.round(bbox.y - pad);
    const width = Math.max(20, Math.round(bbox.width + pad * 2));
    const height = Math.max(20, Math.round(bbox.height + pad * 2));
    const viewBox = `${minX} ${minY} ${width} ${height}`;

    // 2. Clone the element to isolate it
    const clone = element.cloneNode(true);
    // Remove temporary inspector classes/attributes
    clone.classList.remove('svg-inspect-highlight', 'svg-inspect-active');

    // 3. Collect associated <defs> (gradients, clip paths, etc.)
    const serializer = new XMLSerializer();
    const rawContent = serializer.serializeToString(clone);
    const defsContent = extractReferencedDefs(rawContent, rootSvg);

    // 4. Determine suggested name & category
    const id = element.id || '';
    const dataName = element.getAttribute('data-name') || '';
    const dataCategory = element.getAttribute('data-category') || 'Объекты';
    const suggestedTitle = dataName || (id ? formatIdToTitle(id) : 'Векторный элемент');

    return {
      title: suggestedTitle,
      category: dataCategory,
      viewBox,
      width,
      height,
      svgContent: rawContent,
      defs: defsContent,
      bbox: { x: minX, y: minY, width, height },
    };
  } catch (err) {
    console.error('Error extracting SVG node:', err);
    return null;
  }
}

/**
 * Extract <linearGradient>, <radialGradient>, <clipPath> referenced inside the SVG snippet
 */
export function extractReferencedDefs(svgSnippet, rootSvg) {
  if (!rootSvg) return '';
  const rootDefs = rootSvg.querySelector('defs');
  if (!rootDefs) return '';

  // Find all url(#id) matches in snippet
  const regex = /url\(#([a-zA-Z0-9_-]+)\)/g;
  const matches = [...svgSnippet.matchAll(regex)].map((m) => m[1]);
  const uniqueIds = Array.from(new Set(matches));

  if (uniqueIds.length === 0) return '';

  const serializer = new XMLSerializer();
  let collectedDefs = '';

  uniqueIds.forEach((id) => {
    const defElem = rootDefs.querySelector(`#${id}`);
    if (defElem) {
      collectedDefs += serializer.serializeToString(defElem) + '\n';
    }
  });

  return collectedDefs;
}

/**
 * Format camelCase or snake_case ID to human readable title
 */
function formatIdToTitle(id) {
  return id
    .replace(/[_-]/g, ' ')
    .replace(/([a-z])([A-Z])/g, '$1 $2')
    .replace(/^\w/, (c) => c.toUpperCase());
}

/**
 * Compile isolated component into complete standalone <svg> string for preview
 */
export function buildStandaloneSvg(svgContent, viewBox = '0 0 200 200', defs = '') {
  return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="${viewBox}" width="100%" height="100%">
    ${defs ? `<defs>${defs}</defs>` : ''}
    ${svgContent}
  </svg>`;
}
