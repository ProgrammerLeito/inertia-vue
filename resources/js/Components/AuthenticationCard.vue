<template>
    <div id="fondoLogin" class="min-h-screen flex flex-col justify-center items-center px-2 sm:pt-0 bg-[url('../../public/img/sistema.png')] bg-no-repeat bg-cover bg-center">
        <div class="z-50 w-full flex flex-col justify-center items-center drop-shadow-[0_15px_15px_rgba(50,50,50,0.40)]">
            <div class="z-50 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm w-full sm:max-w-md flex flex-col justify-center items-center py-4 rounded-t-lg">
                <!-- <link href='https://webbeta.balinsa.com/font/alphamalemodern.ttf' rel="stylesheet" type="text/css"> -->
                <!-- <link href='http://127.0.0.1:8000/font/Poppins-Bold.ttf' rel="stylesheet" type="text/css"> -->
                <h1 id="textoIniciarSesion" class="text-gray-700 dark:text-gray-200 font-bold text-2xl my-4 tracking-wider">INICIAR SESIÓN</h1>
                <slot name="logo" />
            </div>
    
            <div class="w-full sm:max-w-md px-6 py-4 bg-white/80 overflow-hidden rounded-b-lg dark:bg-gray-800/80 z-50 backdrop-blur-sm">
                <slot />
            </div>
        </div>
    </div>
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

#textoIniciarSesion{
    font-family: "Poppins";
}
</style>