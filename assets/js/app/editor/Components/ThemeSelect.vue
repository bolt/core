<template>
    <section class="user__theme">
        <h3>Themes:</h3>
        <ul class="user__theme--themes">
            <!-- eslint-disable-next-line vuejs-accessibility/click-events-have-key-events, vuejs-accessibility/no-static-element-interactions -->
            <li v-for="(theme, index) in themes" :key="index" class="theme" @click="selectTheme(theme.value)">
                <h5 class="theme--name">{{ theme.name }}</h5>
                <div class="theme--palette">
                    <span v-for="(color, key) in theme.colors" :key="key" :style="`background-color: ${color}`"></span>
                </div>
            </li>
        </ul>
    </section>
</template>

<script setup lang="ts">
const themes = [
    {
        name: 'Default',
        value: 'default',
        colors: ['#1a5597', '#053b79', '#0c223b', '#ffffff'],
    },
    {
        name: 'Light',
        value: 'light',
        colors: ['#1a5597', '#053b79', '#ffffff', '#f9f9f9'],
    },
];

function selectTheme(val: string) {
    const stylesheet = document.querySelector<HTMLLinkElement>('#theme');
    const href = stylesheet?.getAttribute('href');
    if (!stylesheet || !href) {
        return;
    }

    const url = href.split('theme-')[0];
    const theme = `${url}theme-${val}.css`;
    stylesheet.setAttribute('href', theme);
}
</script>
