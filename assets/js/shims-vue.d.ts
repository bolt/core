declare module '*.vue' {
    import type { DefineComponent } from 'vue';
    const component: DefineComponent<Record<string, never>, Record<string, never>, Record<string, never>>;
    export default component;
}

declare module '*.scss' {
    const content: string;
    export default content;
}

declare module '*.css' {
    const content: string;
    export default content;
}

declare module 'baguettebox.js' {
    type BaguetteBoxGalleryItem = {
        eventHandler: (event: Event) => void;
        imageElement: HTMLAnchorElement;
    };

    type BaguetteBoxGallery = BaguetteBoxGalleryItem[];

    type BaguetteBoxOptions = {
        animation?: 'slideIn' | 'fadeIn' | false;
        afterShow?: () => void;
        afterHide?: () => void;
        async?: boolean;
        bodyClass?: string;
        buttons?: boolean | 'auto';
        captions?: boolean | ((this: BaguetteBoxGallery, element: HTMLAnchorElement) => string);
        closeX?: string;
        filter?: RegExp;
        fullScreen?: boolean;
        ignoreClass?: string;
        leftArrow?: string;
        noScrollbars?: boolean;
        onChange?: (currentIndex: number, imagesCount: number) => void;
        overlayBackgroundColor?: string;
        preload?: number;
        rightArrow?: string;
        titleTag?: boolean;
    };

    const baguetteBox: {
        run(selector: string, options?: BaguetteBoxOptions): BaguetteBoxGallery[];
        show(index?: number, gallery?: BaguetteBoxGallery): boolean;
        hide(): void;
        destroy(): void;
    };

    export default baguetteBox;
}

declare module 'vue-trumbowyg' {
    import type { App, DefineComponent } from 'vue';

    type TrumbowygConfigValue =
        | string
        | number
        | boolean
        | null
        | TrumbowygConfigValue[]
        | {
              [key: string]: TrumbowygConfigValue;
          };

    const component: DefineComponent<
        {
            modelValue: string | null;
            config?: Record<string, TrumbowygConfigValue>;
            svgPath?: string | boolean;
            disabled?: boolean;
        },
        Record<string, never>,
        Record<string, never>
    > & {
        install(app: App, name?: string): void;
    };

    export function trumbowygPlugin(app: App, name?: string): void;
    export { component };

    export default component;
}
