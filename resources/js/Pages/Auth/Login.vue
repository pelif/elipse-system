<template>
    <div 
        class="min-h-screen w-full flex items-center justify-center bg-login bg-cover bg-center bg-no-repeat relative"
    >
        <!-- Overlay for better contrast -->
        <div class="absolute inset-0 bg-black/30 backdrop-blur-[1px]"></div>

        <!-- Flash Messages Overlay -->
        <div class="absolute top-8 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-3 w-full max-w-md px-4">
            <div v-if="page.props.flash.message" class="p-4 w-full opacity-90 bg-linear-to-br from-green-500/20 to-green-600/20 text-white rounded shadow-lg text-center backdrop-blur-md border border-green-500/30">
                {{ page.props.flash.message }}
            </div>

            <div v-if="errorMessage" class="p-4 w-full opacity-90 bg-linear-to-br from-red-500/20 to-red-600/20 text-white rounded shadow-lg text-center backdrop-blur-md border border-red-500/30">
                {{ errorMessage }}
            </div>  
        </div>

        <!-- Login Card -->
        <div class="relative z-10 w-full max-w-4xl mx-4 flex flex-col md:flex-row bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/20">
            
            <!-- Branding Side -->
            <div class="md:w-1/2 p-12 flex flex-col opacity-90 items-center justify-center bg-linear-to-br from-amber-500/20 to-orange-600/20">
                <div class="mb-8 transform transition-transform hover:scale-150 duration-500">
                    <img src="/images/elipse-logo.png" width="300" height="100" alt="Elipse Logo" class="drop-shadow-2xl">
                </div>               
                <div class="text-center text-white/90">
                    <h2 class="text-2xl font-bold mb-2">Bem-vindo ao Elipse ERP</h2>
                    <p class="text-white/70">A gestão inteligente para o seu negócio.</p>
                </div>
            </div>

            <!-- Form Side -->
            <div class="md:w-1/2 p-12 bg-white/95 flex flex-col justify-center opacity-92">
                <div class="mb-10 text-center md:text-left">
                    <h1 class="text-3xl font-bold text-gray-800">Login</h1>
                    <p class="text-gray-500 mt-2">Entre com suas credenciais para acessar.</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">E-mail</label>
                        <input 
                            type="email" 
                            placeholder="seu@email.com" 
                            v-model="form.email"    
                            class="w-full px-5 py-4 bg-gray-50 border rounded-xl focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all duration-300"
                            :class="form.errors.email ? 'border-red-500' : 'border-gray-200'"
                        >
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Senha</label>
                        <input 
                            type="password" 
                            v-model="form.password"    
                            placeholder="********" 
                            class="w-full px-5 py-4 bg-gray-50 border rounded-xl focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 outline-none transition-all duration-300"
                            :class="form.errors.password ? 'border-red-500' : 'border-gray-200'"
                        >
                    </div>

                    <div class="flex flex-col items-center justify-center">
                        <div id="recaptcha-container" :class="{'border border-red-500 rounded-lg p-1': form.errors['g-recaptcha-response']}"></div>
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <a href="#" class="text-sm text-amber-600 hover:text-amber-700 font-semibold transition-colors">Esqueceu a senha?</a>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-4 bg-linear-to-r from-amber-500 via-orange-500 to-amber-600 bg-size-[200%_auto] bg-left text-white font-bold rounded-xl shadow-lg shadow-amber-500/30 hover:bg-right hover:shadow-amber-900/50 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-1000"
                        :disabled="form.processing"
                    >
                        Entrar na conta
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-gray-500">
                    &copy; {{ new Date().getFullYear() }} Exitus Comunicação. Todos os direitos reservados.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const recaptchaSiteKey = computed(() => page.props.google.recaptcha.site_key);

const errorMessage = computed(() => {
    if (page.props.flash.error) return page.props.flash.error;
    
    const errors = Object.values(form.errors);
    if (errors.length > 0) return errors[0];
    
    return null;
});

const form = useForm({
    email: '',
    password: '', 
    'g-recaptcha-response': ''
});

window.onRecaptchaSuccess = (token) => {
    form['g-recaptcha-response'] = token;
}

window.onRecaptchaExpired = () => {
    form['g-recaptcha-response'] = '';
}

onMounted(() => {
    const renderRecaptcha = () => {
        const container = document.getElementById('recaptcha-container');
        if (window.grecaptcha && window.grecaptcha.render && container && !container.hasChildNodes()) {
            window.grecaptcha.render('recaptcha-container', {
                'sitekey': page.props.google.recaptcha.site_key,
                'callback': window.onRecaptchaSuccess,
                'expired-callback': window.onRecaptchaExpired
            });
        }
    };

    window.onRecaptchaLoad = renderRecaptcha;

    const alreadyLoaded = document.querySelector('script[src^="https://www.google.com/recaptcha/api.js"]');
    if (!alreadyLoaded) {
        const script = document.createElement('script');
        script.src = "https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit";
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    } else {
        if (window.grecaptcha && window.grecaptcha.render) {
            renderRecaptcha();
        } else {
            const oldLoad = window.onRecaptchaLoad;
            window.onRecaptchaLoad = () => {
                if (oldLoad) oldLoad();
                renderRecaptcha();
            };
        }
    }
});

const submit = () => {
    form.post(route('login.auth'));    
}
</script>

<style scoped>
/* Glassmorphism effects */
.backdrop-blur-xl {
    backdrop-filter: blur(23px);
    -webkit-backdrop-filter: blur(24px);
}

.bg-login {
    background-image: url('/images/login_bg.jpeg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
</style>