<template>
    <div class="rich-text-editor">
        <div v-if="editor" class="editor-toolbar bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-t-lg p-2 flex flex-wrap gap-1">
            <!-- Text formatting -->
            <button
                @click="editor.chain().focus().toggleBold().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('bold') }"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                type="button"
                title="Bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h8a4 4 0 100-8H6v8zm0 0h8a4 4 0 110 8H6v-8z"/>
                </svg>
            </button>
            
            <button
                @click="editor.chain().focus().toggleItalic().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('italic') }"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                type="button"
                title="Italic">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6h4m-2 0v12"/>
                </svg>
            </button>
            
            <button
                @click="editor.chain().focus().toggleUnderline().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('underline') }"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                type="button"
                title="Underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10m-5-4v-12m-4 0v8a4 4 0 008 0V5"/>
                </svg>
            </button>
            
            <button
                @click="editor.chain().focus().toggleStrike().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('strike') }"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                type="button"
                title="Strike">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-6-6h12"/>
                </svg>
            </button>
            
            <div class="w-px bg-gray-300 dark:bg-gray-600 mx-1"></div>
            
            <!-- Headings -->
            <button
                @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('heading', { level: 1 }) }"
                class="px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm font-semibold"
                type="button"
                title="Heading 1">
                H1
            </button>
            
            <button
                @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('heading', { level: 2 }) }"
                class="px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm font-semibold"
                type="button"
                title="Heading 2">
                H2
            </button>
            
            <button
                @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('heading', { level: 3 }) }"
                class="px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm font-semibold"
                type="button"
                title="Heading 3">
                H3
            </button>
            
            <button
                @click="editor.chain().focus().setParagraph().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('paragraph') }"
                class="px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm"
                type="button"
                title="Paragraph">
                P
            </button>
            
            <div class="w-px bg-gray-300 dark:bg-gray-600 mx-1"></div>
            
            <!-- Lists -->
            <button
                @click="editor.chain().focus().toggleBulletList().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('bulletList') }"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                type="button"
                title="Bullet List">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            
            <button
                @click="editor.chain().focus().toggleOrderedList().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('orderedList') }"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                type="button"
                title="Ordered List">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 6h10M7 12h10M7 18h10"/>
                </svg>
            </button>
            
            <button
                @click="editor.chain().focus().toggleBlockquote().run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('blockquote') }"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                type="button"
                title="Quote">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 17h3l2-4V7h-6v6h3l-2 4zm-6 0h3l2-4V7H7v6h3l-2 4z"/>
                </svg>
            </button>
            
            <div class="w-px bg-gray-300 dark:bg-gray-600 mx-1"></div>
            
            <!-- Text Alignment -->
            <button
                @click="editor.chain().focus().setTextAlign('left').run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive({ textAlign: 'left' }) }"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                type="button"
                title="Align Left">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 12h12M3 18h18"/>
                </svg>
            </button>
            
            <button
                @click="editor.chain().focus().setTextAlign('center').run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive({ textAlign: 'center' }) }"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                type="button"
                title="Align Center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M7 12h10M4 18h16"/>
                </svg>
            </button>
            
            <button
                @click="editor.chain().focus().setTextAlign('right').run()"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive({ textAlign: 'right' }) }"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                type="button"
                title="Align Right">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M9 12h12M3 18h18"/>
                </svg>
            </button>
            
            <div class="w-px bg-gray-300 dark:bg-gray-600 mx-1"></div>
            
            <!-- Link -->
            <button
                @click="setLink"
                :class="{ 'bg-gray-200 dark:bg-gray-600': editor.isActive('link') }"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                type="button"
                title="Add Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            </button>
            
            <button
                @click="editor.chain().focus().unsetLink().run()"
                :disabled="!editor.isActive('link')"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                type="button"
                title="Remove Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101M9.172 13.828a4 4 0 015.656 0l4 4a4 4 0 01-5.656 5.656l-1.1-1.1"/>
                </svg>
            </button>
            
            <div class="w-px bg-gray-300 dark:bg-gray-600 mx-1"></div>
            
            <!-- Undo/Redo -->
            <button
                @click="editor.chain().focus().undo().run()"
                :disabled="!editor.can().undo()"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                type="button"
                title="Undo">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                </svg>
            </button>
            
            <button
                @click="editor.chain().focus().redo().run()"
                :disabled="!editor.can().redo()"
                class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                type="button"
                title="Redo">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10h-10a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6"/>
                </svg>
            </button>
        </div>
        
        <editor-content 
            :editor="editor" 
            class="prose prose-sm max-w-none dark:prose-invert editor-content border border-t-0 border-gray-300 dark:border-gray-600 rounded-b-lg bg-white dark:bg-gray-800 min-h-[300px] p-4"
        />
    </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Underline from '@tiptap/extension-underline'
import TextAlign from '@tiptap/extension-text-align'
import Placeholder from '@tiptap/extension-placeholder'
import { watch, onBeforeUnmount } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: 'Start writing your content here...'
    },
    minHeight: {
        type: String,
        default: '300px'
    }
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit.configure({
            heading: {
                levels: [1, 2, 3]
            }
        }),
        Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                class: 'text-blue-500 hover:text-blue-600 underline'
            }
        }),
        Underline,
        TextAlign.configure({
            types: ['heading', 'paragraph']
        }),
        Placeholder.configure({
            placeholder: props.placeholder
        })
    ],
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML())
    },
    editorProps: {
        attributes: {
            class: 'focus:outline-none'
        }
    }
})

// Watch for external changes to modelValue
watch(() => props.modelValue, (value) => {
    if (editor.value && value !== editor.value.getHTML()) {
        editor.value.commands.setContent(value, false)
    }
})

const setLink = () => {
    const url = window.prompt('Enter URL:')
    if (url) {
        editor.value.chain().focus().setLink({ href: url }).run()
    }
}

onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy()
    }
})
</script>

<style scoped>
:deep(.ProseMirror) {
    min-height: v-bind(minHeight);
}

:deep(.ProseMirror p.is-editor-empty:first-child::before) {
    content: attr(data-placeholder);
    float: left;
    color: #adb5bd;
    pointer-events: none;
    height: 0;
}

:deep(.ProseMirror:focus) {
    outline: none;
}

/* Ensure proper spacing for lists */
:deep(.ProseMirror ul),
:deep(.ProseMirror ol) {
    padding-left: 1.5rem;
}

:deep(.ProseMirror ul) {
    list-style-type: disc;
}

:deep(.ProseMirror ol) {
    list-style-type: decimal;
}

:deep(.ProseMirror blockquote) {
    border-left: 3px solid #e5e7eb;
    padding-left: 1rem;
    margin-left: 0;
}

:deep(.dark .ProseMirror blockquote) {
    border-left-color: #4b5563;
}

/* Heading styles */
:deep(.ProseMirror h1) {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

:deep(.ProseMirror h2) {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

:deep(.ProseMirror h3) {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

:deep(.ProseMirror p) {
    margin-bottom: 0.75rem;
}
</style>