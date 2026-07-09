import mitt from 'mitt';

type Events = {
    'slugify-from-title': { source: string };
    'generate-from-title': { sources: string[]; active: boolean };
};

export const eventBus = mitt<Events>();
