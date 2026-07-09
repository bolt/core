declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        val: string | number | boolean | null | undefined;
        edit: boolean;
        locked: boolean;
        buttonText: string;
        icon: string;
        generateSlug: () => void;
        generate: () => void;
        compile: () => void;
        counter: number;
        allowMore: boolean;
        filelist: string;
        directory: string;
        csrfToken: string;
        labels: Record<string, string>;
        extraFields: Record<string, string | boolean | number | null>;
        shouldGenerateFromTitle: () => boolean;
        editSlug: () => void;
        lockSlug: () => void;
        limit: number;
        extensions: string[];
        attributesLink: string;
        readonly: boolean;
        $emit: (event: string, ...args: Array<string | number | boolean | null | undefined>) => void;
    }
}

export {};
