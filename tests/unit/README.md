# Frontend unit tests

Vitest + `@vue/test-utils` over the Vue 3 admin components, the Pinia stores,
the filters and the app-level scripts. `npm run test:unit` to run,
`npm run test:unit:watch` while working, `npm run test:unit:coverage` for the
gate CI enforces.

They were written against the Vue 2.7 components ahead of the Vue 3 migration,
so that the upgrade had a regression baseline rather than a round of manual
clicking, and were ported along with it.

## Conventions

1. **Assert on rendered DOM and emitted events.** Never reach into the
   internals of the component under test — `<script setup>` does not expose
   them anyway.

   Calling `.vm` on a *stub* is a different thing and is fine — that is how a
   spec drives the fake multiselect or draggable (`choose`, `reorder`, `type`).
   Those calls go through the stub's own API, which `helpers/stubs.ts` owns.
2. **Stub third-party components by the name they register under**, via
   `helpers/stubs.ts` — never `vi.mock('vue-multiselect')`. Package names and
   internals change across major versions; the component's name does not. Two
   exceptions: `Date.spec.ts` drives the real flatpickr because the component
   reaches into the DOM the library builds, and `Markdown.spec.ts` mocks
   `easymde`, which the component constructs directly rather than through a
   component.
3. **Component specs take their DOM fixtures from `helpers/dom.ts`.** Several
   components query the document outside themselves — Slug for its title
   fields, File and Image for the Bootstrap modal — because in production they
   mount against Twig-rendered markup. The specs under `app/` are the exception:
   those scripts expect a whole page, so each lays out the markup it needs.
4. **Seed store state through `helpers/store.ts`**, then assert through the
   rendered DOM or the recorded `writes()` and `calls()`. The factories build a
   real Pinia from the app's own stores, so real getters and actions run.
5. **Vue warnings fail the test.** A spec that deliberately provokes one
   declares it with `expectVueWarning()`, which also asserts the warning
   happened.
6. **Every `it.skip` and `it.todo` names what unblocks it**, in the comment
   above it. `grep -rnE 'it\.(skip|todo)' .` is the list.
7. **Tests must not depend on their order.** CI runs them shuffled. Clean up
   in the global teardown (`setup.ts`, plus `restoreMocks` and `unstubGlobals`
   in `vitest.config.ts`) rather than at the end of a test, where a failing
   assertion skips it.

## Layout

    setup.ts        global setup: jQuery globals, jsdom gaps, per-test teardown
    helpers/
      store.ts      Pinia factories over the real stores
      stubs.ts      third-party component stubs, keyed by registered name
      dom.ts        the Twig-rendered surroundings components query
      bus.ts        driving and observing the Slug ⇄ Text event bus
      jquery.ts     waiting for the scripts' jQuery ready handlers
      warnings.ts   Vue warning policy
      errors.ts     capturing errors thrown in lifecycle hooks
    app/            the non-component scripts (common, ajax-save, modal,
                    notifications, patience-is-a-virtue, save-on-ctrl-s)
    components/     the SFCs, mirroring assets/js/app/
    composables/  utils/  filters/  services/  store/
