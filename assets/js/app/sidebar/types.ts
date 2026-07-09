export type SidebarSubmenuItem = {
    name?: string | null;
    icon?: string;
    editLink?: string;
    link?: string;
};

export type SidebarMenuItem = {
    type?: 'separator' | string;
    name: string;
    icon?: string;
    link?: string;
    link_new?: string | null;
    link_listing?: string;
    singleton?: boolean;
    submenu?: SidebarSubmenuItem[] | null;
};
