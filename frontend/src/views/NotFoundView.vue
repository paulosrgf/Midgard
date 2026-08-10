<template>
  <main
    class="flex-1 flex flex-col items-center justify-center w-full max-w-[720px] mx-auto border-r border-zinc-900 bg-black min-h-screen text-center p-8"
  >
    <h1 class="font-serif text-[120px] leading-none text-zinc-800 tracking-tighter mb-4">404</h1>
    <h2 class="font-serif text-2xl uppercase tracking-widest text-zinc-100 mb-6">
      Além das fronteiras de Midgard
    </h2>
    <p
      class="font-sans text-sm text-zinc-500 uppercase tracking-widest max-w-md leading-relaxed mb-10"
    >
      Você vagou pelas névoas de Niflheim e encontrou um beco sem saída. Esta página não existe na
      lenda.
    </p>
    <router-link
      to="/"
      class="bg-zinc-100 text-black font-sans text-xs font-bold uppercase tracking-[0.2em] px-8 py-4 hover:bg-red-700 hover:text-white transition-colors"
    >
      Retornar ao Salão
    </router-link>
  </main>
</template>
