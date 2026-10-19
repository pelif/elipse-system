<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />

        <div class="w-[50%] mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">Comissionado</h4>
                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80"
                    :href="route('comissioned-agents.index')"
                >
                    Voltar
                </Link>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <div class="text-xs uppercase text-gray-500 font-bold">Nome</div>
                    <div class="text-lg font-semibold text-gray-900">{{ comissionedAgent?.name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase text-gray-500 font-bold">Comissão Padrão (%)</div>
                    <div class="text-gray-700">{{ comissionedAgent?.defaultComission ?? '-' }}%</div>
                </div>
                <div>
                    <div class="text-xs uppercase text-gray-500 font-bold">Dias de Pagamento</div>
                    <div class="text-gray-700">{{ comissionedAgent?.paymentDays ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase text-gray-500 font-bold">Observações</div>
                    <div class="text-gray-700 whitespace-pre-wrap">{{ comissionedAgent?.observations || '-' }}</div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const { props } = usePage();

const pageTitle = 'Comissionados';
const pageType = 'Cadastros';

const comissionedAgent = computed(() => props.comissionedAgent);
</script>
