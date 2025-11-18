(function () {
  // safeSetHTML: use DOMPurify if available, otherwise refuse to set raw HTML with tags
  window.safeSetHTML = function (el, html) {
    if (!el) return;

    // If DOMPurify is loaded, use it
    if (window.DOMPurify && typeof DOMPurify.sanitize === 'function') {
      try {
        el.innerHTML = DOMPurify.sanitize(html);
      } catch (e) {
        console.error('safeSetHTML: DOMPurify.sanitize failed', e);
        el.textContent = html;
      }
      return;
    }

    // Fallback: if string contains HTML tags, do NOT assign raw innerHTML
    // instead set textContent and warn in console so developer can include DOMPurify.
    var containsTags = /<[a-z][\s\S]*>/i.test(html);
    if (containsTags) {
      console.warn('safeSetHTML: DOMPurify not found. Refusing to set HTML with tags. Include DOMPurify or sanitize server-side.');
      el.textContent = html;
      return;
    }

    // Safe: no tags present, assign as text using textContent to avoid parsing
    el.textContent = html;
  };
})();
