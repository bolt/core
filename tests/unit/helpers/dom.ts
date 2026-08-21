/**
 * DOM fixtures.
 *
 * Several components query the DOM *outside* themselves, because in production
 * they are mounted against Twig-rendered markup rather than a parent SFC. These
 * helpers stand in for that surrounding markup.
 *
 * Nothing else in the suite should write to document.body directly — resetDom()
 * runs after every test (see ../setup.ts) and only knows about what it created.
 */

function appendFixture(html: string): HTMLElement {
    const holder = document.createElement('div');
    holder.dataset.testFixture = 'true';
    holder.innerHTML = html;
    document.body.appendChild(holder);
    return holder;
}

/**
 * Slug.vue reads the title field(s) it generates from straight off the document:
 *   document.querySelector(`input[name='fields[${element}]']`).value
 * Keys are field names, values are the field's current value.
 */
export function withTitleFields(fields: Record<string, string>): void {
    const inputs = Object.entries(fields)
        .map(([name, value]) => `<input name="fields[${name}]" value="${value}">`)
        .join('');
    appendFixture(inputs);
}

/** ThemeSelect.vue swaps the href on `#theme`. */
export function withThemeLink(href: string): void {
    appendFixture(`<link id="theme" rel="stylesheet" href="${href}">`);
}

/** _SidebarToggler.vue toggles `is-slim` on `.admin`, and throws if it is absent. */
export function withAdminShell(): void {
    appendFixture('<div class="admin"></div>');
}

/**
 * File.vue and Image.vue drive this Bootstrap modal through raw innerHTML
 * writes, and Image.vue's upload-from-url flow hangs its handler on the
 * `#modalButtonAccept` button.
 */
export function withResourcesModal(): void {
    appendFixture(`
        <div id="resourcesModal" class="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title"></h5></div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button id="modalButtonAccept" type="button">OK</button>
                    </div>
                </div>
            </div>
        </div>
    `);
}

/** Embed.vue reads document.getElementsByName('_csrf_token')[0].value. */
export function withCsrfToken(token: string): void {
    appendFixture(`<input name="_csrf_token" value="${token}">`);
}

/**
 * Clears the document between tests. Called from the global afterEach.
 *
 * This empties the whole body rather than just the fixtures above, because
 * components mounted with `attachTo` stay in the document afterwards. Several
 * components look elements up globally — Password.vue finds its own input with
 * `$('#' + this.id)`, Slug.vue queries for the title fields — so a leftover
 * mount from an earlier test would answer the query first.
 */
export function resetDom(): void {
    document.body.innerHTML = '';
}
