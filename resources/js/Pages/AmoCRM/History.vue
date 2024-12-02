<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head } from '@inertiajs/vue3';

    defineProps({
        history_logs: {
            type: Object,
        },
    });

</script>

<template>
    <Head title="История" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                История (Лог)
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                        <tr>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Дата и время</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Действие</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Результат</th>
                            <!-- <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Info</th> -->
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in history_logs" :key="item.id">
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ item.action_date }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ item.action }} "{{ item.action }}"</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ item.result }}</td>
                            <!-- <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ item.info }}</td> -->
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
