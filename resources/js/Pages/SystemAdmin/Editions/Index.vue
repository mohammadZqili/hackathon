<script setup>
import { Head, useForm, router } from '@inertiajs/vue3'
import Default from '../../../Layouts/Default.vue'
import Datatable from '../../../Components/Datatable.vue'
import { ref, computed } from 'vue'

const props = defineProps({
    editions: Object,
})

const deleteForm = useForm({})

const deleteEdition = (edition) => {
    if (confirm(`Are you sure you want to delete the edition "${edition.name}"?`)) {
        deleteForm.delete(route('system-admin.editions.destroy', edition.id))
    }
}

const formatDate = (startDate, endDate) => {
    const start = new Date(startDate)
    const end = new Date(endDate)
    const startMonth = start.toLocaleDateString('en-US', { month: 'short' })
    const startDay = start.getDate()
    const endMonth = end.toLocaleDateString('en-US', { month: 'short' })
    const endDay = end.getDate()
    
    if (startMonth === endMonth) {
        return `${startMonth} ${startDay} - ${endDay}`
    }
    return `${startMonth} ${startDay} - ${endMonth} ${endDay}`
}

const tableColumns = [
    {
        accessorKey: 'name',
        header: 'Hackathon Name',
        cell: ({ row }) => {
            return h('span', { class: 'text-seagreen font-medium' }, row.original.name)
        }
    },
    {
        accessorKey: 'year',
        header: 'Year',
        cell: ({ row }) => row.original.year
    },
    {
        accessorKey: 'registration_dates',
        header: 'Registration Dates',
        cell: ({ row }) => {
            return h('span', { class: 'text-seagreen' }, 
                formatDate(row.original.registration_start_date, row.original.registration_end_date)
            )
        }
    },
    {
        accessorKey: 'teams_count',
        header: 'Teams Count',
        cell: ({ row }) => {
            return h('span', { class: 'text-seagreen' }, row.original.teams_count || 0)
        }
    },
    {
        accessorKey: 'hackathon_admin',
        header: 'Hackathon Admin',
        cell: ({ row }) => {
            const creator = row.original.creator
            if (creator) {
                return h('span', { class: 'text-seagreen' }, creator.name)
            }
            return h('span', { class: 'text-gray-400' }, 'Not assigned')
        }
    },
    {
        accessorKey: 'action',
        header: 'Action',
        cell: ({ row }) => {
            return h('div', { class: 'flex items-center gap-2' }, [
                h('button', {
                    onClick: () => router.visit(route('system-admin.editions.edit', row.original.id)),
                    class: 'text-cadetblue hover:text-cadetblue/80 font-bold transition-colors'
                }, 'Edit'),
                h('span', { class: 'text-cadetblue font-bold' }, '|'),
                h('button', {
                    onClick: () => deleteEdition(row.original),
                    class: 'text-cadetblue hover:text-cadetblue/80 font-bold transition-colors'
                }, 'Delete')
            ])
        }
    }
]

import { h } from 'vue'
</script>

<template>
    <Head title="Hackathon Editions" />
    <Default>
        <div class="flex-1 flex flex-col gap-4">
            <!-- Header -->
            <div class="flex flex-row items-start justify-between flex-wrap p-4 gap-x-0 gap-y-3">
                <div class="w-72 flex flex-col items-start justify-start min-w-[288px]">
                    <h1 class="text-[32px] leading-10 font-bold text-gray-900 dark:text-white font-space-grotesk">Editions</h1>
                </div>
                <a :href="route('system-admin.editions.create')" 
                   class="rounded-xl bg-mintcream-200 dark:bg-gray-700 h-8 overflow-hidden flex flex-row items-center justify-center py-0 px-4 box-border min-w-[84px] max-w-[480px] text-center text-sm hover:bg-mintcream-300 dark:hover:bg-gray-600 transition-colors">
                    <div class="overflow-hidden flex flex-col items-center justify-start">
                        <div class="self-stretch relative leading-[21px] font-medium overflow-hidden text-ellipsis whitespace-nowrap text-gray-900 dark:text-white">Add Edition</div>
                    </div>
                </a>
            </div>

            <!-- Data Table -->
            <div class="flex flex-col items-start justify-start py-3 px-4">
                <div class="self-stretch rounded-xl bg-mintcream-100 dark:bg-gray-800 border-honeydew dark:border-gray-700 border-solid border-[1px] overflow-hidden">
                    <Datatable
                        :data="editions.data"
                        :columns="tableColumns"
                        :pagination="editions"
                        :enable-search="false"
                        :enable-export="false"
                        title=""
                        empty-message="No editions found"
                        empty-description="Get started by creating your first hackathon edition."
                    />
                </div>
            </div>
        </div>
    </Default>
</template>