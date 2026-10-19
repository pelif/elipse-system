<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />

        <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-tools" class="mr-2" />
                    Abrir OS
                </h4>
                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('orders.index')"
                >
                    Voltar
                </Link>
            </div>

            <form class="p-6 max-h-[calc(100vh-12rem)] overflow-y-auto" @submit.prevent>
                <h5 class="font-bold text-gray-700 text-xl px-2">Ordem de Serviço</h5>
                <div class="container-form">
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-code" class="input-label">Código da OS</label>
                            <input id="os-code" type="text" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="display(order.osCode)" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-job-code" class="input-label">Código do Job</label>
                            <input id="os-job-code" type="text" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="display(order.jobCode)" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-customer" class="input-label">Cliente</label>
                            <select id="os-customer" class="input-select" :class="{ 'bg-gray-50': formDisabled }" :value="customerValue" :disabled="formDisabled">
                                <option value="">—</option>
                                <option v-if="order.customerId" :value="order.customerId">{{ customerLabel }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-type" class="input-label">Tipo de OS</label>
                            <input id="os-type" type="text" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="display(order.osType)" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                    <div class="group-form col-span-2">
                        <div class="input-container">
                            <label for="os-event-title" class="input-label">Título do evento/serviço</label>
                            <input id="os-event-title" type="text" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="display(order.eventTitle)" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                </div>

                <h5 class="font-bold text-gray-700 text-xl mt-8 px-2">Agenda</h5>
                <div class="container-form">
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-event-date" class="input-label">Data do evento</label>
                            <input id="os-event-date" type="date" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="eventDate" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-start" class="input-label">Início</label>
                            <input id="os-start" type="time" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="startClock" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-end" class="input-label">Término</label>
                            <input id="os-end" type="time" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="endClock" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-duration" class="input-label">Duração</label>
                            <input id="os-duration" type="number" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="durationValue" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                    <div class="group-form col-span-2">
                        <div class="input-container">
                            <label for="os-location" class="input-label">Local físico</label>
                            <input id="os-location" type="text" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="display(order.location)" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                </div>

                <h5 class="font-bold text-gray-700 text-xl mt-8 px-2">Plataforma</h5>
                <div class="container-form">
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-platform" class="input-label">Onde o evento vai acontecer</label>
                            <select id="os-platform" class="input-select" :class="{ 'bg-gray-50': formDisabled }" :value="platformValue" :disabled="formDisabled">
                                <option value="">—</option>
                                <option v-for="option in platformChoices" :key="option" :value="option">{{ option }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-platform-link" class="input-label">Link de acesso</label>
                            <input id="os-platform-link" type="text" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="display(order.platformLink)" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                </div>

                <h5 class="font-bold text-gray-700 text-xl mt-8 px-2">Contato e Produção</h5>
                <div class="container-form">
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-participants" class="input-label">Nº de participantes</label>
                            <input id="os-participants" type="number" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="participantsValue" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-organizer" class="input-label">Organizador</label>
                            <select id="os-organizer" class="input-select" :class="{ 'bg-gray-50': formDisabled }" :value="organizerValue" :disabled="formDisabled">
                                <option value="">—</option>
                                <option v-for="organizer in organizerChoices" :key="organizer.id" :value="organizer.id">
                                    {{ organizer.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-phone" class="input-label">Telefone</label>
                            <input id="os-phone" type="text" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="organizerPhone" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="os-email" class="input-label">E-mail</label>
                            <input id="os-email" type="text" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="organizerEmail" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                    <div class="group-form col-span-2">
                        <div class="input-container">
                            <label for="os-communication" class="input-label">Cuidados na comunicação</label>
                            <textarea id="os-communication" class="input-text" :class="{ 'bg-gray-50': formDisabled }" rows="4" :value="display(order.communicationNotes)" :disabled="formDisabled" :readonly="formDisabled"></textarea>
                        </div>
                    </div>
                    <div class="group-form col-span-2">
                        <div class="input-container">
                            <label for="os-production" class="input-label">Informações para produção</label>
                            <textarea id="os-production" class="input-text" :class="{ 'bg-gray-50': formDisabled }" rows="4" :value="display(order.productionNotes)" :disabled="formDisabled" :readonly="formDisabled"></textarea>
                        </div>
                    </div>
                    <div class="group-form col-span-2">
                        <div class="input-container">
                            <label for="os-file-link" class="input-label">Link para briefing e arquivo</label>
                            <input id="os-file-link" type="text" class="input-text" :class="{ 'bg-gray-50': formDisabled }" :value="display(order.fileLink)" :disabled="formDisabled" :readonly="formDisabled">
                        </div>
                    </div>
                </div>
            </form>

            <div class="px-6 pb-6 flex justify-end">
                <button type="button" class="btn-submit mr-2 bg-gray-700">
                    Cancelar
                </button>
                <button type="button" class="btn-submit mr-2" @click="enableFormEditing">
                    Editar
                </button>
                <button type="button" class="btn-submit">
                    Salvar
                </button>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import { formatCellphone } from '@/helpers/masks';

const PLATFORM_OPTIONS = [
    'Não definida',
    'Landing Page Exitus',
    'Eventials',
    'Zoom',
    'Plataforma do Cliente',
];

const pageTitle = 'Abrir OS';
const pageType = 'Fluxo de Trabalho';

const page = usePage();

const formDisabled = ref(true);

const enableFormEditing = () => {
    formDisabled.value = false;
};

const order = computed(() => {
    if (page.props.order && typeof page.props.order === 'object') {
        return page.props.order;
    }

    return {};
});

const jobOrganizers = computed(() => {
    if (Array.isArray(page.props.jobOrganizers)) {
        return page.props.jobOrganizers;
    }

    return [];
});

const display = (value) => {
    if (value === null || value === undefined) {
        return '';
    }

    return String(value);
};

const splitDateTime = (value) => {
    if (!value) {
        return { date: '', time: '' };
    }
    const normalized = String(value).trim().replace(' ', 'T');
    const date = new Date(normalized);
    if (Number.isNaN(date.getTime())) {
        return { date: '', time: '' };
    }
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return {
        date: date.getFullYear() + '-' + month + '-' + day,
        time: hours + ':' + minutes,
    };
};

const eventDate = computed(() => splitDateTime(order.value.startTime).date);
const startClock = computed(() => splitDateTime(order.value.startTime).time);
const endClock = computed(() => splitDateTime(order.value.endTime).time);

const durationValue = computed(() => {
    if (order.value.duration === null || order.value.duration === undefined) {
        return '';
    }

    return order.value.duration;
});

const participantsValue = computed(() => {
    if (order.value.numberParticipants === null || order.value.numberParticipants === undefined) {
        return 0;
    }

    return order.value.numberParticipants;
});

const customerValue = computed(() => {
    if (!order.value.customerId) {
        return '';
    }

    return order.value.customerId;
});

const customerLabel = computed(() => {
    if (order.value.customerLegalName) {
        return order.value.customerLegalName;
    }

    return 'Cliente';
});

const platformChoices = computed(() => {
    const options = [];
    for (let index = 0; index < PLATFORM_OPTIONS.length; index++) {
        options.push(PLATFORM_OPTIONS[index]);
    }
    const current = order.value.platformDestination;
    if (current && options.includes(current) === false) {
        options.push(current);
    }

    return options;
});

const platformValue = computed(() => {
    if (!order.value.platformDestination) {
        return '';
    }

    return order.value.platformDestination;
});

const organizerChoices = computed(() => {
    const list = [];
    for (let index = 0; index < jobOrganizers.value.length; index++) {
        list.push(jobOrganizers.value[index]);
    }
    const currentId = order.value.organizerId;
    if (!currentId) {
        return list;
    }
    for (let index = 0; index < list.length; index++) {
        if (list[index].id === currentId) {
            return list;
        }
    }
    let name = 'Organizador';
    if (order.value.organizerName) {
        name = order.value.organizerName;
    }
    let phone = '';
    if (order.value.organizerPhone) {
        phone = order.value.organizerPhone;
    }
    let email = '';
    if (order.value.organizerEmail) {
        email = order.value.organizerEmail;
    }
    list.push({
        id: currentId,
        name: name,
        phone: phone,
        email: email,
    });

    return list;
});

const organizerValue = computed(() => {
    if (!order.value.organizerId) {
        return '';
    }

    return order.value.organizerId;
});

const selectedOrganizer = computed(() => {
    const currentId = order.value.organizerId;
    if (!currentId) {
        return null;
    }
    for (let index = 0; index < organizerChoices.value.length; index++) {
        if (organizerChoices.value[index].id === currentId) {
            return organizerChoices.value[index];
        }
    }

    return null;
});

const maskedPhone = (raw) => {
    if (!raw) {
        return '';
    }
    const digits = String(raw).replace(/\D/g, '');
    if (digits === '') {
        return '';
    }

    return formatCellphone(digits);
};

const organizerPhone = computed(() => {
    if (selectedOrganizer.value && selectedOrganizer.value.phone) {
        return maskedPhone(selectedOrganizer.value.phone);
    }

    return maskedPhone(order.value.organizerPhone);
});

const organizerEmail = computed(() => {
    if (selectedOrganizer.value && selectedOrganizer.value.email) {
        return selectedOrganizer.value.email;
    }
    if (order.value.organizerEmail) {
        return order.value.organizerEmail;
    }

    return '';
});
</script>
