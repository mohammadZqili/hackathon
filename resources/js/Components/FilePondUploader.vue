<script setup>
import vueFilePond from 'vue-filepond'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginPdfPreview from 'filepond-plugin-pdf-preview'
import 'filepond-plugin-pdf-preview/dist/filepond-plugin-pdf-preview.min.css'
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation'
import FilePondPluginImageResize from 'filepond-plugin-image-resize'
import FilePondPluginImageCrop from 'filepond-plugin-image-crop'

const FilePond = vueFilePond(
    FilePondPluginImageExifOrientation,
    FilePondPluginFileValidateType,
    FilePondPluginImagePreview,
    FilePondPluginFileValidateSize,
    FilePondPluginPdfPreview,
    FilePondPluginImageCrop,
    FilePondPluginImageResize,
)

const props = defineProps({
    name: {
        type: String,
        required: true
    },
    label: {
        type: String,
        required: true
    },
    labelIdle: {
        type: String,
        default: 'Drop files here...'
    },
    acceptedFileTypes: {
        type: Array,
        default: () => ['image/jpeg', 'image/png', 'application/pdf', 'image/x-icon']
    },
    maxFileSize: {
        type: String,
        default: '5MB'
    },
    allowMultiple: {
        type: Boolean,
        default: false
    },
    maxFiles: {
        type: Number,
        default: 1
    },
    server: {
        type: Object,
        required: true
    },
    required: {
        type: Boolean,
        default: false
    },
    files: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['processfile', 'removefile'])
</script>


<template>
    <div class="space-y-2">
        <label v-if="label" class="block text-sm font-medium text-gray-600 dark:text-gray-300 text-center">
            {{ label }}
            <span class="text-xs text-gray-500 block mt-1">
                ({{acceptedFileTypes.map(type => type.split('/')[1].toUpperCase()).join(', ')}}
                <template v-if="allowMultiple"> - Max files: {{ maxFiles }}</template>)
            </span>
        </label>

        <file-pond 
            :name="name" 
            :allow-multiple="allowMultiple" 
            :max-files="maxFiles"
            :accepted-file-types="acceptedFileTypes" 
            :max-file-size="maxFileSize" 
            :server="server" 
            :files="files"
            :credits="null" 
            :allow-pdf-preview="true" 
            :label-idle="labelIdle || `Drop files here or <span class='filepond--label-action'>Browse</span>`"
            :image-preview-height="allowMultiple ? 50 : 100"
            :image-crop-aspect-ratio="'1:1'"
            :image-resize-target-width="100"
            :image-resize-target-height="100"
            :style-panel-layout="allowMultiple ? 'compact' : 'compact circle'"
            :style-load-indicator-position="'center bottom'"
            :style-button-remove-item-position="'center bottom'"
            :pdf-component-extra-params="'toolbar=0'" 
            :class="allowMultiple ? 'filepond-multiple' : 'max-w-xs'"
            :style-panel-aspect-ratio="allowMultiple ? '1:1' : '1:1'"
            @processfile="(error, file) => $emit('processfile', error, file)"
            @removefile="(error, file) => $emit('removefile', error, file)" 
        />
    </div>
</template>

<style>
/* Fixed small size for gallery */
.filepond-multiple {
    max-width: 100%;
}

.filepond-multiple .filepond--root {
    max-height: 180px !important;
    margin-bottom: 0;
}

.filepond-multiple .filepond--panel-root {
    min-height: 120px !important;
    max-height: 180px !important;
}

/* Very compact layout for multiple files - 5 columns */
.filepond-multiple .filepond--item {
    width: calc(20% - 0.25rem) !important;
    margin: 0.125rem !important;
}

.filepond-multiple .filepond--item .filepond--item-panel {
    aspect-ratio: 1;
    background-size: cover !important;
}

/* Small image previews */
.filepond-multiple .filepond--image-preview-wrapper {
    height: 50px !important;
}

.filepond-multiple .filepond--file {
    padding: 0 !important;
}

@media (max-width: 768px) {
    .filepond-multiple .filepond--item {
        width: calc(25% - 0.25rem) !important;
    }
}

/* Limit height and add scroll for gallery */
.filepond-multiple .filepond--list {
    max-height: 150px !important;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Hide file info for cleaner look */
.filepond-multiple .filepond--file-info {
    display: none;
}

.filepond-multiple .filepond--file-status {
    display: none;
}

/* Smaller drop label */
.filepond-multiple .filepond--drop-label {
    font-size: 0.75rem !important;
    padding: 0.5rem !important;
    min-height: 2rem !important;
}

/* Smaller action buttons */
.filepond-multiple .filepond--action-remove-item {
    width: 20px !important;
    height: 20px !important;
}

/* Style scrollbar */
.filepond-multiple .filepond--list::-webkit-scrollbar {
    width: 4px;
}

.filepond-multiple .filepond--list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.filepond-multiple .filepond--list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
}

.filepond-multiple .filepond--list::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Dark mode support */
.dark .filepond--panel-root {
    background-color: rgb(31 41 55);
    border-color: rgb(75 85 99);
}

.dark .filepond--drop-label {
    color: rgb(156 163 175);
}

.dark .filepond--label-action {
    color: rgb(147 197 253);
}

.dark .filepond-multiple .filepond--list::-webkit-scrollbar-track {
    background: rgb(31 41 55);
}

.dark .filepond-multiple .filepond--list::-webkit-scrollbar-thumb {
    background: rgb(75 85 99);
}

/* Prevent overflow */
.filepond--wrapper {
    overflow: hidden;
}
</style>
