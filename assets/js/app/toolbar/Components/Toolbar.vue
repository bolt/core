<template>
    <div class="admin__toolbar--body" :class="contrast ? 'is-light' : 'is-dark'" role="toolbar">
        <div class="toolbar-item btn-group toolbar-item__brand">
            <img src="/assets/images/bolt_logo_dashboard.svg" alt="⚙️ Bolt" height="26" />
        </div>

        <div v-if="isImpersonator" class="toolbar-impersonation">
            <a :href="urlPaths['bolt_dashboard'] + '?_switch_user=_exit'" class="btn btn-warning">
                <i class="fas fa-sign-out-alt fa-fw"></i>
                {{ labels['action.stop_impersonating'] }}
            </a>
        </div>

        <div class="toolbar-item toolbar-item__site">
            <a href="/" class="text-decoration-none" target="_blank">
                <i class="fas fa-sign-out-alt"></i>{{ labels['action.view_site'] }}
            </a>
        </div>

        <form :action="urlPaths['bolt_dashboard']" class="toolbar-item toolbar-item__filter input-group">
            <label for="global-search" class="sr-only">{{ labels['general.label.search'] }}</label>
            <input
                id="global-search"
                type="text"
                class="form-control"
                :placeholder="labels['listing.placeholder_search']"
                :title="labels['listing.placeholder_search']"
                name="filter"
                :value="filterValue"
                required
            />
            <div class="input-group-append">
                <button
                    class="btn btn-tertiary toolbar-item__filter--button"
                    type="submit"
                    :title="labels['listing.button_search']"
                >
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <div class="toolbar-item btn-group toolbar-item__profile">
            <button
                class="btn user profile__dropdown-toggler dropdown-toggle d-flex align-items-center"
                type="button"
                data-bs-toggle="dropdown"
                data-display="static"
                aria-haspopup="true"
                aria-expanded="false"
            >
                <img v-if="avatar" :src="avatar" class="rounded-circle me-2" alt="User avatar" />
                <i v-else class="fas fa-user"></i>{{ labels['general.greeting'] }}
                <template v-if="isImpersonator">
                    &nbsp;<span style="font-style: italic;">({{ labels['general.is_impersonator'] }})</span>
                </template>
            </button>
            <div class="profile__dropdown dropdown-menu dropdown-menu-right">
                <ul>
                    <li>
                        <a :href="urlPaths['bolt_profile_edit']" class="text-decoration-none">
                            <i class="fas fa-user-edit fa-fw"></i>
                            {{ labels['action.edit_profile'] }}
                        </a>
                    </li>
                    <li>
                        <a :href="urlPaths['bolt_logout']" class="text-decoration-none">
                            <i class="fas fa-sign-out-alt fa-fw"></i>
                            {{ labels['action.logout'] }}
                        </a>
                    </li>
                    <hr />
                    <li>
                        <a href="https://boltcms.io/" class="text-decoration-none" target="_blank">
                            <i class="fas fa-globe-americas fa-fw"></i>
                            {{ labels['about.visit_bolt'] }}
                        </a>
                    </li>
                    <li>
                        <a href="https://docs.bolt.cm/" class="text-decoration-none" target="_blank">
                            <i class="fas fa-book fa-fw"></i>
                            {{ labels['about.bolt_documentation'] }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
const tinycolor = require('tinycolor2');

export default {
    name: 'Toolbar',
    props: {
        siteName: String,
        menu: Array,
        labels: Object,
        urlPaths: Object,
        backendPrefix: String,
        isImpersonator: Boolean,
        filterValue: String,
        avatar: String,
    },
    computed: {
        contrast() {
            const color = tinycolor(this.toolbarColor);
            return color.isLight();
        },
        createMenu() {
            return this.menu.filter(item => {
                return (
                    (!item.singleton && item.singular_name) ||
                    (item.singleton && (item.submenu === null || item.submenu.length < 1))
                );
            });
        },
        toolbarColor() {
            return this.$store.getters['general/toolbarColor'];
        },
    },
    created() {
        const color = getComputedStyle(document.body).getPropertyValue('--admin-toolbar');
        this.$store.dispatch('general/toolbarColor', color);
    },
};
</script>
