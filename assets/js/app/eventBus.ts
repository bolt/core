import mitt from 'mitt';

export type Events = {
    'slugify-from-title': void;
    'generate-from-title': boolean;
};

export const eventBus = mitt<Events>();
