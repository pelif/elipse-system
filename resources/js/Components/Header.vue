<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue"
import { usePage, Link, router } from '@inertiajs/vue3'
import { randomBg, printInitials } from '@/helpers/styles'

defineProps({
    pageTitle: String,
    pageType: String
})

const page = usePage()
const appName = page.props.appName

const userName = computed(() => page.props.auth.user.name)
const userId = computed(() => page.props.auth.user.id)
const profile = computed(() => page.props.auth.profile)

const isMenuOpen = ref(false)
const menuRef = ref(null)
const avatarBg = randomBg()

const arrowClass = computed(() => {
    if (isMenuOpen.value) {
        return ['text-orange-500', 'rotate-180']
    }
    return 'text-gray-400'
})

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value
}

const handleClickOutside = (event) => {
    if (menuRef.value && !menuRef.value.contains(event.target)) {
        isMenuOpen.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})

const logout = () => {
    router.post(route('logout'))
}
</script>

<template>
    <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 shrink-0 shadow-sm">
        <div class="flex items-center gap-4">
            <h2 class="text-xl font-semibold text-gray-800">{{ pageTitle }}</h2>
            <span
                class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-bold uppercase tracking-widest">
                {{ pageType }}
            </span>
        </div>

        <div class="flex items-center gap-6">
            <button class="relative p-2 text-gray-400 hover:text-orange-500 transition-colors bg-gray-50 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 border-2 border-white rounded-full"></span>
            </button>

            <div ref="menuRef" class="relative flex items-center gap-3 pl-6 border-l border-gray-200 cursor-pointer" @click.stop="toggleMenu">
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ userName }}</p>
                    <p class="text-xs text-gray-500">{{ profile }}</p>
                </div>
                <div class="relative">
                    <div class="w-8 h-8 rounded flex items-center justify-center text-white font-bold text-xs"
                        :class="avatarBg">
                        {{ printInitials(userName) }}
                    </div>
                    <div class="absolute -bottom-1 -right-1 bg-green-500 w-3 h-3 rounded-full border-2 border-white">
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 transition-all"
                    :class="arrowClass"
                    fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>

                <div
                    v-if="isMenuOpen"
                    class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50"
                    @click.stop
                >
                    <Link
                        :href="route('profile.show', userId)"
                        class="px-4 py-2.5 text-sm hover:bg-orange-50 hover:text-orange-600 transition-colors flex items-center gap-3 text-gray-700"
                        @click="isMenuOpen = false"
                    >
                        <font-awesome-icon icon="fa-solid fa-user" class="w-4 h-4" />
                        Minha Conta
                    </Link>

                    <div class="px-4 py-2.5 text-sm flex items-center gap-3 text-gray-400 cursor-default">
                        <font-awesome-icon icon="fa-solid fa-id-badge" class="w-4 h-4" />
                        Perfil: {{ profile }}
                    </div>

                    <div class="border-t border-gray-100 mt-1 pt-1">
                        <button
                            type="button"
                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-orange-50 hover:text-orange-600 transition-colors flex items-center gap-3 text-gray-700"
                            @click="logout"
                        >
                            <font-awesome-icon icon="fa-solid fa-power-off" class="w-4 h-4" />
                            Sair
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>