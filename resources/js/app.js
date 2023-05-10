import './bootstrap';
import '../css/app.css';

import {createApp, h} from 'vue';
import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import VueApexCharts from "vue3-apexcharts";
import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import PrimeVue from "primevue/config";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

/* import the fontawesome core */
import {library} from '@fortawesome/fontawesome-svg-core';

/* import specific icons */
import {faAngleDown} from '@fortawesome/free-solid-svg-icons';
import {faAngleUp} from "@fortawesome/free-solid-svg-icons";
import {faInfo} from "@fortawesome/free-solid-svg-icons";
import {faSpinner} from "@fortawesome/free-solid-svg-icons";
import {faCheck} from "@fortawesome/free-solid-svg-icons";
import {faCircleXmark} from "@fortawesome/free-regular-svg-icons";
import {faClock} from "@fortawesome/free-regular-svg-icons";
import {faCalendarDays} from "@fortawesome/free-regular-svg-icons";
import {faRotateLeft} from "@fortawesome/free-solid-svg-icons";
import {faCalendarXmark} from "@fortawesome/free-regular-svg-icons";
import {faSwatchbook} from "@fortawesome/free-solid-svg-icons";

/* add icons to the library */
library.add(faAngleDown);
library.add(faAngleUp);
library.add(faInfo);
library.add(faSpinner);
library.add(faCheck);
library.add(faCircleXmark);
library.add(faClock);
library.add(faCalendarDays);
library.add(faRotateLeft);
library.add(faCalendarXmark);
library.add(faSwatchbook);

createInertiaApp({
    title: (title) => `${title}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({el, App, props, plugin}) {
        return createApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(PrimeVue)
            .use(VueApexCharts)
            .component('font-awesome-icon', FontAwesomeIcon)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
