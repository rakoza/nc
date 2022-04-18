<template>

    <section class="section has-background-light py-4">
        <nav class="level">
            <!-- Left side -->
            <div class="level-left">
                <div class="level-item">
                    <slot name="icon"></slot>
                    <p class="title is-size-3">
                        {{ title }}
                        <span class="ml-2 is-size-5 has-text-primary" v-if="subtitle">{{ subtitle }}</span>
                    </p>
                </div>
                <div class="level-item">
                    <slot name="left"></slot>
                </div>
            </div>
            <!-- Right side -->
            <div class="level-right">
                <slot></slot>
            </div>
        </nav>
    </section>

</template>

<script>
export default {
    props: ['title','subtitle'],
    data() {
        return {
        };
    },
};
</script>
