<template>
    <button type="button" class="button is-primary" @click="click">
        <slot>Sačuvaj</slot>
        <i class="fas fa-save" style="margin-left: 5px;"></i>
    </button>
</template>

<script>
export default {
    methods: {
        click() {
            this.$emit('click')
        },
    }
};
</script>
