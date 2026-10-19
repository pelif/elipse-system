import './bootstrap'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { ZiggyVue } from 'ziggy-js'


import {
  faUser,
  faGear,
  faDatabase,
  faChartDiagram,
  faHouse,
  faChevronDown,
  faChevronUp,
  faBox,
  faSitemap,
  faFileContract,
  faTableList,
  faList,
  faVideo,
  faMagnifyingGlass,
  faMoneyBill,
  faClockRotateLeft,
  faEnvelope,
  faSquarePlus,
  faFilePen,
  faEye,
  faTrashCan,
  faXmark,
  faCircleExclamation,
  faCheck,
  faKey,
  faPowerOff,
  faShoePrints,
  faArrowLeft,
  faRotateRight,
  faAnglesUp,
  faBarsProgress,
  faAward,
  faCircleInfo,
  faTags,
  faIdBadge,
  faCubes,
  faEllipsisVertical,
  faTools,

} from '@fortawesome/free-solid-svg-icons'

library.add(
  faUser,
  faGear,
  faDatabase,
  faChartDiagram,
  faHouse,
  faChevronDown,
  faChevronUp,
  faBox,
  faSitemap,
  faFileContract,
  faTableList,
  faList,
  faVideo,
  faMagnifyingGlass,
  faMoneyBill,
  faClockRotateLeft,
  faEnvelope,
  faSquarePlus,
  faFilePen,
  faEye,
  faTrashCan,
  faXmark,
  faCircleExclamation,
  faCheck,
  faKey,
  faPowerOff,
  faShoePrints,
  faArrowLeft,
  faRotateRight,
  faAnglesUp,
  faBarsProgress,
  faAward,
  faCircleInfo,
  faTags,
  faIdBadge,
  faCubes,
  faEllipsisVertical,
  faTools,
);

// Carrega dinamicamente as páginas Vue
// O padrão ./Pages/**/*.vue busca todos os arquivos .vue em Pages e subpastas
createInertiaApp({
  // Função que resolve o nome da página para o componente Vue
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue')
    const importPage = pages[`./Pages/${name}.vue`]

    if (!importPage) {
      throw new Error(`Página não encontrada: ${name}`)
    }
    return importPage().then(module => module.default || module)
  },

  // Função de setup que inicializa a aplicação Vue
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)  // Ativa o plugin Inertia no Vue
      .use(ZiggyVue)
      .component('font-awesome-icon', FontAwesomeIcon)
      .mount(el)    // Monta no elemento #app do HTML
  },
})
