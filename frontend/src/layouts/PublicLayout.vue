<script setup>
import Navbar from '../components/layout/Navbar.vue'
import Footer from '../components/layout/Footer.vue'
</script>

<template>
    <div class="public-layout">
        <Navbar />

        <main>
            <slot />
        </main>

        <Footer />
    </div>
</template>
