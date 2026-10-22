// In-page PDF reader for library documents (PDF.js). Pages render lazily as they scroll into
// view, so a phone on mobile data only fetches and draws what the reader actually looks at.
// The document URL is an access-checked endpoint; PDF.js never runs scripts inside PDFs.
import * as pdfjsLib from 'pdfjs-dist';
import workerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = workerUrl;

function setStatus(viewer, text, isError = false) {
    const status = viewer.querySelector('[data-pdf-status]');
    if (status) {
        status.textContent = text;
        status.classList.toggle('text-red-700', isError);
    }
}

async function mount(viewer) {
    const pagesEl = viewer.querySelector('[data-pdf-pages]');
    setStatus(viewer, 'Loading document…');

    let pdf;
    try {
        pdf = await pdfjsLib.getDocument({ url: viewer.dataset.pdfSrc, withCredentials: true, isEvalSupported: false }).promise;
    } catch (e) {
        setStatus(viewer, 'The document could not be loaded. Check your connection and refresh the page.', true);
        return;
    }

    setStatus(viewer, `${pdf.numPages} page${pdf.numPages === 1 ? '' : 's'}`);

    const render = async (holder) => {
        if (holder.dataset.rendered) return;
        holder.dataset.rendered = '1';
        const page = await pdf.getPage(Number(holder.dataset.page));
        const width = holder.clientWidth || pagesEl.clientWidth;
        const base = page.getViewport({ scale: 1 });
        const ratio = Math.min(window.devicePixelRatio || 1, 2);
        const viewport = page.getViewport({ scale: (width / base.width) * ratio });

        const canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        canvas.style.width = '100%';
        canvas.style.pointerEvents = 'none'; // clicks and right-clicks fall through to the protected viewer
        canvas.draggable = false;
        canvas.setAttribute('role', 'img');
        canvas.setAttribute('aria-label', `Page ${holder.dataset.page}`);
        holder.replaceChildren(canvas);
        holder.style.minHeight = '';
        await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                observer.unobserve(entry.target);
                render(entry.target).catch(() => {
                    entry.target.textContent = 'This page could not be displayed.';
                });
            }
        });
    }, { rootMargin: '400px 0px' });

    for (let i = 1; i <= pdf.numPages; i++) {
        const holder = document.createElement('div');
        holder.dataset.page = String(i);
        holder.className = 'mb-3 rounded border border-gray-200 bg-white shadow-sm';
        holder.style.minHeight = `${Math.round(pagesEl.clientWidth * 1.41)}px`; // A4-ish placeholder
        pagesEl.appendChild(holder);
        observer.observe(holder);
    }
}

// Makes casual saving of the page images harder: no right-click menu ("Save image as"), no dragging
// a page out, no selecting or copying, and the canvases ignore clicks. This is a deterrent, not
// security: anyone who can read a page can still screenshot it. Real access control is on the server.
function protect(viewer) {
    const stop = (event) => event.preventDefault();
    ['contextmenu', 'dragstart', 'selectstart', 'copy', 'cut'].forEach((name) => viewer.addEventListener(name, stop));
    viewer.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && ['s', 'p', 'u'].includes(event.key.toLowerCase())) {
            event.preventDefault();
        }
    });
}

document.querySelectorAll('[data-pdf-src]').forEach((viewer) => {
    protect(viewer);
    mount(viewer);
});
