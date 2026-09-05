<script setup>
import PublicLayout from '../layouts/PublicLayout.vue'

import HeroSection from '../components/home/HeroSection.vue'
import ServicesSection from '../components/home/ServicesSection.vue'
import ProjectsSection from '../components/home/ProjectsSection.vue'
import ProcessSection from '../components/home/ProcessSection.vue'
import CtaSection from '../components/home/CtaSection.vue'
import ContactSection from '../components/home/ContactSection.vue'
</script>

<template>
    <PublicLayout>
        <HeroSection />

        <ServicesSection />

        <ProjectsSection />

        <ProcessSection />

        <CtaSection />

        <ContactSection />
    </PublicLayout>
</template>
