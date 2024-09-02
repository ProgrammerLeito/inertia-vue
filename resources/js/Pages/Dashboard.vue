<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref } from 'vue';
import { Pie } from 'vue-chartjs';
import { Chart as ChartJs, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';
ChartJs.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);
 
const props = defineProps({
    data: { type: Array }
});

const motivationalQuote = ref('');
const greeting = ref('');

const motivationalQuotes = [
    '¡Si puedes soñarlo, puedes lograrlo!',
    'El éxito no es la clave de la felicidad. La felicidad es la clave del éxito.',
    'Cree en ti mismo y todo será posible.',
    'La única forma de hacer un gran trabajo es amar lo que haces.',
    'No cuentes los días, haz que los días cuenten.',
    'La vida es 10% lo que me sucede y 90% cómo reacciono a ello.',
    'El único lugar donde el éxito viene antes que el trabajo es en el diccionario.',
    'El único límite para tus logros es tu propia mente.',
    'Las oportunidades no ocurren, las creas.',
    'No esperes por el momento adecuado, ¡haz que el momento sea adecuado!',
    'El éxito es la suma de pequeños esfuerzos repetidos día tras día.',
    'Lo único imposible es aquello que no intentas.',
    'El fracaso es la oportunidad de comenzar de nuevo, pero esta vez más inteligentemente.',
    'El camino hacia el éxito y hacia el fracaso son casi exactamente iguales.',
    'La suerte es lo que sucede cuando la preparación se encuentra con la oportunidad.',
    'No se trata de cuántas veces te derriban, sino de cuántas veces te levantas.',
    'Si quieres algo que nunca tuviste, tienes que hacer algo que nunca has hecho.',
    'La confianza en sí mismo es el primer secreto del éxito.',
    'La motivación nos impulsa a comenzar y el hábito nos permite continuar.',
    'El optimismo es la fe que conduce al logro. Nada puede hacerse sin esperanza y confianza.',
    'El verdadero éxito viene cuando te levantas después de cada caída.',
    'El mayor placer en la vida es hacer lo que la gente dice que no puedes hacer.',
    'No importa lo lento que vayas, siempre y cuando no te detengas.',
    'Nunca es demasiado tarde para ser lo que podrías haber sido.',
    'Haz lo que puedas, con lo que tengas, donde estés.',
    'El momento en que quieres renunciar es el momento en que necesitas seguir adelante.',
    'La perseverancia no es una carrera larga; son muchos sprints cortos, uno tras otro.',
    'El éxito no es la clave de la felicidad. La felicidad es la clave del éxito. Si amas lo que estás haciendo, tendrás éxito.',
    'No busques oportunidades extraordinarias. Cada día, haz algo que te acerque un poco más a tus sueños.',
    'Cada pequeño paso en la dirección correcta te acerca a la cima de la montaña.',
];

const getRandomQuote = () => {
    const index = Math.floor(Math.random() * motivationalQuotes.length);
    return motivationalQuotes[index];
};

motivationalQuote.value = getRandomQuote();

const getGreeting = () => {
    const currentHour = new Date().getHours(); // Obtener la hora actual
    if (currentHour < 12) {
        return 'Buenos Días'; // Saludo para la mañana
    } else if (currentHour < 18) {
        return 'Buenas Tardes'; // Saludo para la tarde
    } else {
        return 'Buenas Noches'; // Saludo para la noche
    }
};

// Asignar el saludo al ref
greeting.value = getGreeting();
 
</script>
 
<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-white">
                Inicio
            </h2>
        </template>
 
        <div id="fondoLogin" class="relative min-h-[calc(100vh-171px)] h-full flex flex-col justify-center items-center px-2 sm:pt-0 bg-[url('../../public/img/welcome_ini.jpg')] bg-no-repeat bg-cover bg-center">
            <div class="bg-white rounded-lg shadow-black dark:shadow-white shadow-2xl z-50 h-auto md:h-[450px] md:px-2 px-0 w-auto md:w-[1000px]">
                <div class="flex flex-col p-4 gap-2 md:gap-2 justify-center items-center">
                    <video src="../../../public/img/balinsa.mp4" alt="Logooo" style="width: 700px; height: 300px; object-fit: contain;" autoplay muted loop></video>
                    <!-- <div class="font-bold text-md">
                        ¡{{ greeting }} {{$page.props.auth.user.name}}!
                    </div> -->
                    <div class="md:text-5xl text-2xl font-extrabold text-center" id="B_logo">
                        INDUSTRIAS BALINSA - E.I.R.L.
                    </div>
                    <!-- <div class="md:text-xl text-md font-semibold text-center">
                        {{ motivationalQuote }}
                    </div> -->
                    <div class="md:text-xl text-xl font-semibold text-center">
                        Piura - Trujillo - Chimbote
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
html #fondoLogin::after {
    width: 100%;
    height: 100%;
    content: '';
    background-color: #d8d8d899;
    position: absolute;
    backdrop-filter: blur(4px);
    z-index: 1;
}

html.dark #fondoLogin::after {
    width: 100%;
    height: 100%;
    content: '';
    background-color: #21212499;
    position: absolute;
    backdrop-filter: blur(4px);
    z-index: 1;
}
</style>