<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

onMounted(() => {
    const modoOscuroLocalStorage = localStorage.getItem('modoOscuro') === 'true';
    document.documentElement.classList.toggle('dark', modoOscuroLocalStorage);
});
</script>

<template>
    <div class="w-full h-screen flex items-start">
        <div class="fondoLogin2 bg-no-repeat bg-cover bg-center bg-[url('../../public/img/1.png')] relative w-1/2 h-full hidden md:flex flex-col justify-between">
            <div class="flex flex-1 justify-center m-auto flex-col z-10">
                <h1 id="B_logo" class="text-5xl text-white font-bold my-4">BALINSA E.I.R.L.</h1>
                <p class="text-xl text-white font-normal">Bienvenido al sistema integrado de gestion administrativa.</p>
            </div>
            <div class="w-full flex items-center justify-center text-center z-10 p-2">
                <p class="text-base font-normal text-white">Diseñado y desarrollado por <a href="https://balinsa.com" class="font-extrabold underline underline-offset-2 cursor-pointer">INDUSTRIAS BALINSA E.I.R.L.</a></p>
            </div>
        </div>
        <div class="fondoLogin md:w-1/2 w-full h-full flex flex-col justify-center items-center bg-no-repeat bg-cover bg-center bg-[url('../../public/img/1.png')] md:bg-gray-100 dark:md:bg-gray-800 dark:text-white md:bg-none md:p-20 p-8">
            <form @submit.prevent="submit" class="z-10">
                <div class="w-full flex flex-col max-w-[400px]">
                    <div class="w-full flex flex-col mb-2 items-center">
                        <h3 class="text-2xl font-extrabold mb-2 uppercase md:text-black text-white dark:text-white" id="textoIniciarSesion">Iniciar Sesion</h3>
                        <img src="img/logo_ini.png" alt="Logo" class="w-full p-4 h-full object-cover dark:hidden">
                        <img src="img/logo_ini_dark.png" alt="Logo Dark" class="w-full p-4 h-full object-cover hidden dark:block">
                    </div>

                    <div class="w-full flex flex-col">
                        <p class="dark:text-white font-extrabold text-base text-white md:text-black">Email :</p>
                        <input v-model="form.email" required class="w-full md:text-black text-white text-base md:dark:text-white py-4 my-2 bg-transparent border-b rounded-md border-gray-200 md:border-black dark:border-gray-300 outline-none placeholder:text-white md:placeholder:text-gray-400 dark:md:placeholder:text-gray-400 focus:outline-none" type="email" placeholder="example@balinsa.net"/>
                        <InputError class="mt-2" :message="form.errors.email" />
                        <p class="dark:text-white font-extrabold text-base text-white md:text-black">Contraseña :</p>
                        <input v-model="form.password" required class="w-full md:text-black text-white text-base dark:text-white py-4 my-2 bg-transparent border-b rounded-md border-gray-200 md:border-black dark:border-gray-300 outline-none placeholder:text-white md:placeholder:text-gray-400 dark:md:placeholder:text-gray-400 focus:outline-none" type="password" placeholder="**************">
                        <InputError class="mt-2" :message="form.errors.password" />
                        <div class="flex justify-between whitespace-break-spaces items-center my-2">
                            <div>
                                <Checkbox v-model:checked="form.remember" name="remember" />
                                <span class="ms-2 text-sm text-white md:text-black dark:text-white">Recordarme</span>
                            </div>
                            <Link :href="route('password.request')" class="text-white md:text-blue-700 dark:text-white font-extrabold text-sm flex justify-end">Olvidaste tu contraseña ?</Link>
                        </div>
                    </div>
                    <div class="w-full flex flex-col my-1 items-center">
                        <button class="w-full text-white uppercase font-bold my-2 bg-blue-600 border-2 border-white rounded-md p-3 text-center flex items-center justify-center cursor-pointer" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Ingresar
                        </button>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
</template>

<style>

html .fondoLogin2::after {
      width: 100%;
      height: 100%;
      content: '';
      background-color: #1d1d1d99;
      position: absolute;
  }
  
  html.dark .fondoLogin2::after {
      width: 100%;
      height: 100%;
      content: '';
      background-color: #1d1d1d99;
      position: absolute;
      z-index: 1;
  }
@media only screen and (max-width: 768px) {
  /* Tus estilos aquí */
  html .fondoLogin::after {
      width: 100%;
      height: 100%;
      content: '';
      background-color: #1d1d1d99;
      position: absolute;
  }
  
  html.dark .fondoLogin::after {
      width: 100%;
      height: 100%;
      content: '';
      background-color: #1d1d1d99;
      position: absolute;
      z-index: 1;
  }
}

#textoIniciarSesion{
    font-family: "Poppins";
}
</style>