<template>
    <div class="flex flex-col items-start justify-start py-3 px-4">
        <div class="self-stretch rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-row items-start justify-start">
            <div class="flex-1 flex flex-col items-start justify-start">
                <!-- Table Header -->
                <div class="self-stretch flex flex-col items-start justify-start">
                    <div class="self-stretch flex-1 bg-white dark:bg-gray-800 flex flex-row items-start justify-start">
                        <div v-for="(column, index) in columns" 
                             :key="column.key"
                             class="flex flex-col items-start justify-start py-3 px-4 box-border"
                             :class="column.width || 'flex-1'">
                            <div class="self-stretch relative leading-[21px] font-medium text-sm"
                                 :class="column.headerClass || 'text-gray-700 dark:text-gray-300'">
                                {{ column.label }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Table Body -->
                <div class="self-stretch flex flex-col items-start justify-start">
                    <!-- Empty State -->
                    <div v-if="!data || data.length === 0" 
                         class="self-stretch border-t border-gray-200 dark:border-gray-700 box-border h-[72px] flex flex-row items-center justify-center">
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            {{ emptyMessage || 'No data found.' }}
                        </div>
                    </div>
                    
                    <!-- Data Rows -->
                    <div v-else 
                         v-for="(item, index) in data" 
                         :key="getItemKey(item, index)"
                         class="self-stretch border-t border-gray-200 dark:border-gray-700 box-border h-[72px] flex flex-row items-start justify-start hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        
                        <div v-for="column in columns"
                             :key="column.key"
                             class="h-[72px] flex flex-col items-center justify-center py-2 px-4 box-border"
                             :class="column.width || 'flex-1'">
                            <div class="self-stretch relative leading-[21px]"
                                 :class="column.cellClass || 'text-gray-900 dark:text-white'">
                                <slot :name="column.key" :item="item" :index="index">
                                    {{ getColumnValue(item, column) }}
                                </slot>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    data: {
        type: Array,
        required: true
    },
    columns: {
        type: Array,
        required: true
    },
    emptyMessage: {
        type: String,
        default: 'No data found.'
    },
    keyField: {
        type: String,
        default: 'id'
    }
})

const getItemKey = (item, index) => {
    return item[props.keyField] || index
}

const getColumnValue = (item, column) => {
    if (column.formatter && typeof column.formatter === 'function') {
        return column.formatter(item)
    }
    
    const keys = column.key.split('.')
    let value = item
    
    for (const key of keys) {
        if (value && typeof value === 'object') {
            value = value[key]
        } else {
            return column.defaultValue || '-'
        }
    }
    
    return value || column.defaultValue || '-'
}
</script>