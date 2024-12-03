import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import MainLayout from './Layouts/MainLayout.vue';
import moment from 'moment';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const page = pages[`./Pages/${name}.vue`];
        page.default.layout = MainLayout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin);
        app.config.globalProperties.$filters = {
            formatDate(value) {
                if (!value) {
                    return null;
                }
                return moment(value).format('YYYY-MM-DD hh:mm');
            }
        };
        app.mount(el);
    },
});
