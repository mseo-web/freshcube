<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, router } from '@inertiajs/vue3'

    defineProps({
        leads: {
            type: Object,
        },
    });
    function bindContact(lead_id) {
        router.post(route('contact-binding', lead_id));
    };
</script>

<template>
    <Head title="Выбор сделки" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Выбор сделки
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead>
                                <tr>
                                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">ID</th>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Название</th>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Дата создания</th>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Есть контакт</th>
                                    <th class="relative py-3.5 pl-3 pr-4 sm:pr-0">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr  v-for="lead in leads" :key="lead.id">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">{{ lead.id }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ lead.name }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{
                                            new Date(lead.created_at * 1000).toLocaleString("ru",
                                                {
                                                    second: 'numeric',
                                                    year: 'numeric',
                                                    month: 'numeric',
                                                    day: 'numeric',
                                                    hour: 'numeric',
                                                    minute: 'numeric',

                                                })
                                        }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ lead.contacts!=null ? 'Да' : 'Нет' }}</td>
                                    <td class="whitespace-nowrap py-4 py-4 text-right text-sm sm:pr-0">
                                        <button
                                            type="button"
                                            :disabled="lead.contacts!=null"
                                            @click="bindContact(lead.id)"
                                            :class="lead.contacts!=null ? 'bg-blue-500 text-white font-bold py-2 px-4 rounded opacity-50 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'"
                                        >
                                            Привязать контакт
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
