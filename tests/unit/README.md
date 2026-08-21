# Frontend unit tests

Vitest + `@vue/test-utils` over the Vue 2.7 admin components, the Vuex stores,
the filters and the app-level scripts. `npm run test:unit` to run,
`npm run test:unit:watch` while working, `npm run test:unit:coverage` for the
gate CI enforces.

These tests exist ahead of the Vue 3 migration, so that the upgrade has a
regression baseline rather than a round of manual clicking.

## Conventions

1. **Assert on rendered DOM and emitted events.** Never reach into the
   internals of the component under test. There are four exceptions, each
   commented, and all four are dead code — a computed or method no template
   renders: `Toolbar.createMenu`, `Filter.enableSorting`, `SelectBox.order`,
   `Row.sorting`. They are covered so the migration can see them and decide
   whether they come back or go.

   Calling `.vm` on a *stub* is a different thing and is fine — that is how a
   spec drives the fake multiselect or draggable (`choose`, `reorder`, `type`).
   Those calls go through the stub's own API, which `helpers/stubs.ts` owns.
2. **Stub third-party components by their template tag name**, via
   `helpers/stubs.ts` — never `vi.mock('vue-multiselect')`. Package names and
   internals change across major versions; the tag in the SFC does not. The one
   exception is `Date.spec.ts`, which drives the real flatpickr because the
   component reaches into the DOM the library builds.
3. **Component specs take their DOM fixtures from `helpers/dom.ts`.** Several
   components query the document outside themselves — Slug for its title
   fields, File and Image for the Bootstrap modal — because in production they
   mount against Twig-rendered markup. The specs under `app/` are the exception:
   those scripts expect a whole page, so each lays out the markup it needs.
4. **Seed store state through `helpers/store.ts`**, then assert through the
   rendered DOM or the spied `dispatch`. The factories build a real store from
   the app's own modules, so real getters and mutations run.
5. **Vue warnings fail the test.** Two known defects are allowed for in
   `helpers/warnings.ts`; a spec that deliberately provokes another declares it
   with `expectVueWarning()`, which also asserts the warning happened.
6. **Every `it.skip` names the commit that unblocks it.** They are tests ported
   from `refactor/vue3-composition-api` that only pass once a component fix
   lands. `grep -rn 'it.skip' .` is the list.

## Layout

    setup.ts        global setup: jQuery globals, jsdom gaps, per-test teardown
    helpers/
      store.ts      Vuex store factories over the real modules
      stubs.ts      third-party component stubs, keyed by template tag
      dom.ts        the Twig-rendered surroundings components query
      warnings.ts   Vue warning policy
      errors.ts     capturing errors thrown in lifecycle hooks
      async.ts      flushPromises (test-utils v1 ships none)
    app/            the non-component scripts (common, ajax-save, modal,
                    notifications, patience-is-a-virtue, save-on-ctrl-s)
    components/     the SFCs, mirroring assets/js/app/
    filters/  services/  store/  mixins/

## Notes for the Vue 3 upgrade

The specs call `@vue/test-utils` directly rather than going through a wrapper
layer, so the upgrade edits them. Most of it is mechanical:

| change | scope |
|---|---|
| `propsData:` → `props:` | every mount |
| `stubs:` / `localVue` / `store:` → `global: { stubs, plugins }` | every mount that has them |
| `wrapper.destroy()` → `unmount()`, `findAll(...).at(i)` → `[i]` | mechanical |
| Vuex → Pinia | `helpers/store.ts` and the specs that seed state |
| `$root.$emit`/`$on` → the mitt bus | `helpers/bus.ts`, plus Text and Slug |
| vue-multiselect 2→3, vuedraggable 2→4, trumbowyg 3→4, easymde | `helpers/stubs.ts` |
| `flushPromises` | delete `helpers/async.ts`, import from test-utils |

What is *not* mechanical is anything asserting on component internals — which
is why rule 1 matters. Keeping assertions at the DOM means the specs survive
components becoming `<script setup>`, where internals stop being exposed on the
instance.

`tests/unit/shims-vue.d.ts` and `shims-vuex.d.ts` both go away: the first once
SFCs are `<script setup lang="ts">` and the checker becomes `vue-tsc`, the
second with Vuex.
