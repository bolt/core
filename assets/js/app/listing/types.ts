export type LocalizedString = string | Record<string, string>;

export type ListingImage = {
    thumbnail: string;
    alt?: string | null;
};

export type ListingRecord = {
    id?: number;
    title?: string;
    status?: string;
    authorName?: string;
    createdAt?: string | null;
    publishedAt?: string | null;
    modifiedAt?: string | null;
    fieldValues?: {
        slug?: LocalizedString | null;
    };
    extras?: {
        editLink?: string;
        title?: string;
        feature?: string | null;
        excerpt?: string;
        image?: ListingImage | null;
        icon?: string;
        link?: string;
        statusLink?: string;
        duplicateLink?: string;
        deleteLink?: string;
        singular_name?: string;
        contentTypeOverviewLink?: string;
    };
};

export type BulkAction = {
    key: string;
    value: string;
    selected: boolean;
    class: string;
};
