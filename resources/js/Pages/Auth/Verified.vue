<template>
    <div class="min-h-[calc(100vh-80px)] w-full flex items-center justify-center bg-zinc-50 relative overflow-hidden">
        <!-- Background decorative elements for premium look -->
        <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-amber-500 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-orange-600 rounded-full blur-[120px]"></div>
        </div>

        <div class="relative z-10 w-full max-w-lg mx-4 p-10 bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 flex flex-col items-center text-center">
            <!-- Professional Success Icon -->
            <div class="mb-8 w-24 h-24 bg-linear-to-br from-green-500/20 to-green-600/20 rounded-full flex items-center justify-center text-green-600 shadow-inner">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-12 h-12 drop-shadow-sm">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-800 mb-4 tracking-tight">Senha já criada</h1>
            <p class="text-gray-600 mb-10 leading-relaxed text-lg">
                A senha já foi criada e o e-mail de criação de senha já foi verificado. Sua conta já está ativa e pronta para uso.
            </p>

            <Link 
                :href="route('login')"
                class="w-full py-4 bg-linear-to-r from-amber-500 via-orange-500 to-amber-600 bg-size-[200%_auto] bg-left text-white font-bold rounded-xl shadow-lg shadow-amber-500/30 hover:bg-right hover:shadow-amber-900/50 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-700 text-center"
            >
                Ir para o Login
            </Link>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'; 
</script>