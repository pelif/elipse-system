<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />
        <Grid :jobs="jobs" :can-create-jobs="canCreateJobs" :job-statuses="jobStatuses" />
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import Grid from './Grid.vue';

const page = usePage();

const pageTitle = 'Jobs';
const pageType = 'Fluxo de Trabalho';

const jobs = computed(() => page.props.jobs);
const canCreateJobs = computed(() => page.props.canCreateJobs === true);
const jobStatuses = computed(() => {
    if (Array.isArray(page.props.jobStatuses)) {
        return page.props.jobStatuses;
    }

    return [];
});
</script>
