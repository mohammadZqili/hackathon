import { ref, computed, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

export function useLocalization() {
    const page = usePage()
    
    // Current locale and direction
    const locale = ref(page.props.locale || 'en')
    const direction = ref(page.props.direction || 'ltr')
    
    // Available languages
    const languages = ref({
        en: {
            name: 'English',
            native: 'English',
            flag: '🇬🇧',
            direction: 'ltr',
            code: 'en'
        },
        ar: {
            name: 'Arabic',
            native: 'العربية',
            flag: '🇸🇦',
            direction: 'rtl',
            code: 'ar'
        }
    })
    
    // Current language details
    const currentLanguage = computed(() => languages.value[locale.value])
    
    // Check if RTL
    const isRTL = computed(() => direction.value === 'rtl')
    
    // Translation function
    const t = (key, defaultValue = null, replacements = {}) => {
        // Handle replacements as second argument if no default value
        if (typeof defaultValue === 'object' && defaultValue !== null) {
            replacements = defaultValue
            defaultValue = null
        }
        
        // Get translation from page props
        const translations = page.props.translations || {}
        let translation = translations[key] || defaultValue || key
        
        // Replace placeholders
        Object.keys(replacements).forEach(placeholder => {
            translation = translation.replace(`:${placeholder}`, replacements[placeholder])
        })
        
        return translation
    }
    
    // Switch language
    const switchLanguage = async (newLocale) => {
        if (!languages.value[newLocale]) {
            console.error(`Language ${newLocale} not supported`)
            return
        }
        
        // Make request to switch language
        router.post(`/language/${newLocale}`, {}, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                locale.value = newLocale
                direction.value = languages.value[newLocale].direction
                
                // Update HTML attributes
                updateHtmlAttributes()
            }
        })
    }
    
    // Update HTML attributes
    const updateHtmlAttributes = () => {
        const html = document.documentElement
        html.setAttribute('lang', locale.value)
        html.setAttribute('dir', direction.value)
        
        // Add RTL class for Tailwind
        if (isRTL.value) {
            html.classList.add('rtl')
            html.classList.remove('ltr')
        } else {
            html.classList.add('ltr')
            html.classList.remove('rtl')
        }
    }
    
    // Direction-aware classes
    const directionalClasses = (ltrClass, rtlClass) => {
        return isRTL.value ? rtlClass : ltrClass
    }
    
    // Direction-aware styles
    const directionalStyles = (property, ltrValue, rtlValue) => {
        return {
            [property]: isRTL.value ? rtlValue : ltrValue
        }
    }
    
    // Common directional utilities
    const textAlign = computed(() => isRTL.value ? 'right' : 'left')
    const textAlignOpposite = computed(() => isRTL.value ? 'left' : 'right')
    const marginStart = (value) => isRTL.value ? { marginRight: value } : { marginLeft: value }
    const marginEnd = (value) => isRTL.value ? { marginLeft: value } : { marginRight: value }
    const paddingStart = (value) => isRTL.value ? { paddingRight: value } : { paddingLeft: value }
    const paddingEnd = (value) => isRTL.value ? { paddingLeft: value } : { paddingRight: value }
    const start = (value) => isRTL.value ? { right: value } : { left: value }
    const end = (value) => isRTL.value ? { left: value } : { right: value }
    
    // Flip icon for RTL
    const flipIcon = computed(() => isRTL.value ? 'scale-x-[-1]' : '')
    
    // Initialize on mount
    onMounted(() => {
        // Set initial values from props
        locale.value = page.props.locale || 'en'
        direction.value = page.props.direction || 'ltr'
        
        // Update HTML attributes
        updateHtmlAttributes()
    })
    
    // Watch for changes in page props
    watch(() => page.props.locale, (newLocale) => {
        if (newLocale) {
            locale.value = newLocale
            direction.value = languages.value[newLocale].direction
            updateHtmlAttributes()
        }
    })
    
    return {
        // State
        locale,
        direction,
        languages,
        currentLanguage,
        isRTL,
        
        // Methods
        t,
        switchLanguage,
        
        // Utilities
        directionalClasses,
        directionalStyles,
        textAlign,
        textAlignOpposite,
        marginStart,
        marginEnd,
        paddingStart,
        paddingEnd,
        start,
        end,
        flipIcon,
    }
}