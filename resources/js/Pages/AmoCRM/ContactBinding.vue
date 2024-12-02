<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';

    const props = defineProps({
        lead_id: String
    });

    const form = useForm({
        lead_id: props.lead_id,
        name: "",
        phone: "",
        comment: "",
    });

    const submit = () => {
        form.post(route("contact-binding-create"), {
            preserveScroll: true,
        });
    };
</script>

<template>
    <Head title="Привязка контакта" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Привязка контакта
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <form @submit.prevent="submit" class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2">
                        <input v-model="form.lead_id" type="hidden" />
                        <div class="px-4 py-6 sm:p-8">
                            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="name" class="block text-sm/6 font-medium text-gray-900">Имя контакта</label>
                                <div class="mt-2">
                                <input type="text" v-model="form.name" :message="form.errors.name" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6" />
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="phone" class="block text-sm/6 font-medium text-gray-900">Номер телефона</label>
                                <div class="mt-2">
                                <input type="tel" v-model="form.phone" :message="form.errors.phone" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6" />
                                </div>
                            </div>

                            <div class="col-span-full">
                                <label for="comment" class="block text-sm/6 font-medium text-gray-900">Комментарий</label>
                                <div class="mt-2">
                                <textarea v-model="form.comment" :message="form.errors.comment" required rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6" />
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
                            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Привязать</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
