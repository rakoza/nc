<template>
    <button type="button" class="button is-light" @click="click">
        <slot>Otkaži</slot>
        <i class="fas fa-times" style="margin-left: 5px;"></i>
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
