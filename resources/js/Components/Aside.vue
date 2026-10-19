<script setup lang="ts">

import { reactive, computed, watch } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';

declare const route: (...args: any[]) => string;

// Tipagem simples para evitar erros do Vetur
const page = usePage();
const { props } = page;
const appName = props.appName;
const user = (props as any).auth.user;

const currentPath = computed(() => {
    return page.url.split('?')[0];
});

const menu = reactive([
    {
        label: 'Dashboard',
        icon: 'house',
        open: false,
        route: '/',
        auth: ['admin', 'commercial', 'financial', 'production']
    },
    {
        label: 'Cadastros',
        icon: 'database',
        open: false,
        auth: ['admin', 'commercial', 'financial', 'production'],
        children: [
            {
                label: 'Produtos',
                icon: 'box',
                route: '/cadastros/produtos'
            },
            {
                label: 'Clientes',
                icon: 'user',
                route: '/cadastros/clientes'
            },
            {
                label: 'Comissionados',
                icon: 'award',
                route: '/cadastros/comissionados'
            },
            {
                label: 'Terceiros',
                icon: 'sitemap',
                route: '/cadastros/terceiros'
            },
            {
                label: 'Contratantes',
                icon: 'file-contract',
                route: '/cadastros/contratantes',
            },
            {
                label: 'Organizadores',
                icon: 'table-list',
                route: '/cadastros/organizadores',
            },
            {
                label: 'Categorias',
                icon: 'tags',
                route: '/cadastros/categorias',
            },
            {
                label: 'Recursos',
                icon: 'list',
                route: '/cadastros/recursos'
            }
        ]
    },
    {
        label: 'Fluxo de Trabalho',
        icon: 'chart-diagram',
        open: false,
        auth: ['admin', 'commercial', 'finance', 'production'],
        children: [
            {
                label: 'Novo Job',
                icon: 'video',
                route: '/fluxo/jobs/novo'
            },
            {
                label: 'Consultar Jobs',
                icon: 'magnifying-glass',
                route: '/fluxo/jobs'
            },
            {
                label: 'Consultar OS',
                icon: 'tools',
                route: '/fluxo/os'
            }
        ]
    },
    {
        label: 'Configurações',
        icon: 'gear',
        open: false,
        auth: ['admin'],
        children: [
            {
                label: 'Usuários',
                icon: 'user',
                route: '/configuracoes/usuarios'
            },
            {
                label: 'Status',
                icon: 'bars-progress',
                route: '/configuracoes/fluxo-de-job'
            },
            {
                label: 'Config. de E-mail',
                icon: 'envelope',
                route: '/configuracoes/config-email'
            },
            {
                label: 'Logs do Sistema',
                icon: 'shoe-prints',
                route: '/configuracoes/logs'
            },
        ]
    }
]);

const finded = (itemMenu: string) => {
    return menu.find((item) => item.label === itemMenu);
}

const isRouteActive = (itemRoute: string) => {
    if (itemRoute === '/') {
        return currentPath.value === '/';
    }
    return currentPath.value === itemRoute || currentPath.value.startsWith(itemRoute + '/');
}

const isParentActive = (item: any) => {
    if (item.route) {
        return isRouteActive(item.route);
    }
    if (item.children) {
        return item.children.some((child: any) => isRouteActive(child.route));
    }
    return false;
}

const syncOpenFromUrl = () => {
    menu.forEach((item) => {
        item.open = isParentActive(item);
    });
}

syncOpenFromUrl();
watch(currentPath, syncOpenFromUrl);

const parentStyledItems = (itemMenu: string) => {
    const item = finded(itemMenu);

    if (item && isParentActive(item)) {
        return 'bg-slate-800 border-l-4 border-orange-500';
    }

    return 'hover:bg-slate-800 hover:text-white';
}

const hasMoreSpecificActiveSibling = (child: any, siblings: any[]) => {
    return siblings.some((sibling: any) => {
        if (sibling.route === child.route) {
            return false;
        }
        if (!sibling.route.startsWith(child.route + '/')) {
            return false;
        }
        return isRouteActive(sibling.route);
    });
}

const childStyledItems = (child: any, siblings: any[]) => {
    if (isRouteActive(child.route) && !hasMoreSpecificActiveSibling(child, siblings)) {
        return 'bg-slate-800/60 text-orange-300';
    }
    return 'hover:bg-slate-800 hover:text-white';
}

const openItem = (item: any) => {

    if (item.route) {
        router.visit(item.route);
        return;
    }

    item.open = !item.open;
    uncheckItems(item);
}

const uncheckItems = (item: any) => {
    const otherItems = menu.filter((i) => i.label !== item.label);
    otherItems.forEach((i) => i.open = false);
}

const logout = () => {
    router.post(route('logout'));
}

const hasPermission = (item: any) => {
    if (item.auth) {
        return item.auth.filter((role: string) => role === user.role).length > 0;
    }

    return false;
}
</script>

<template>
    <aside class="w-80 bg-slate-900 text-slate-300 flex flex-col transition-all duration-300">
        <div class="p-6 flex items-center gap-3">
            <div class="flex items-center justify-center">
                <Link href="/">
                    <img src="../../images/elipse-logo.png" width="270" height="90">
                </Link>
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-1 mt-4">
            <template v-for="(item, index) in menu" :key="index">
                <a
                   v-if="hasPermission(item)"
                   href="#"
                   class="flex items-center gap-3 px-4 py-3 text-white rounded-lg transition-colors"
                   :class="parentStyledItems(item.label)"
                   @click.prevent="openItem(item)">
                    <font-awesome-icon :icon="['fas', item.icon]" />
                    {{ item.label }}
                    <font-awesome-icon
                        v-if="item.children?.length > 0 "
                        :icon="['fas', item.open ? 'chevron-up' : 'chevron-down']"
                        class="ml-auto text-sm" />
                </a>

                <div v-if="item.children?.length > 0 && item.open" class="pl-8 space-y-1">
                    <Link
                        v-for="(child, index) in item.children" :key="index"
                       class="flex items-center gap-3 px-4 py-3 text-white rounded-lg transition-colors text-sm"
                       :class="childStyledItems(child, item.children)"
                       :href="child.route">
                        <font-awesome-icon :icon="['fas', child.icon]" />
                        {{ child.label }}
                    </Link>
                </div>
            </template>
        </nav>

        <div class="p-2 border-t border-slate-800">
            <div class="bg-slate-800/50 p-4 rounded-xl border border-slate-700">
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">
                    <Link
                        @click.prevent="logout"
                        class="flex items-center gap-3 px-4 py-3 text-white rounded-lg transition-colors">
                        <font-awesome-icon :icon="['fas', 'power-off']" />
                        Sair
                    </Link>
                </p>

            </div>
        </div>
    </aside>
</template>
