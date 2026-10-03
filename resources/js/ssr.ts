import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import process from 'node:process';
import { createSSRApp, DefineComponent, h } from 'vue';
import { renderToString } from 'vue/server-renderer';
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer(
    (page) =>
        createInertiaApp({
            page,
            render: renderToString,
            title: (title) => (title ? `${title} - ${appName}` : appName),
            resolve: resolvePage,
            setup: ({ App, props, plugin }) =>
                createSSRApp({ render: () => h(App, props) })
                    .use(plugin)
                    .use(ZiggyVue, {
                        ...page.props.ziggy,
                        location: new URL(page.props.ziggy.location),
                    }),
        }),
    { cluster: process.env.INERTIA_SSR_CLUSTER === 'true' },
);

function resolvePage(name: string) {
    const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue');

    return resolvePageComponent<DefineComponent>(`./pages/${name}.vue`, pages);
}
