<template>
    <button type="button" class="button is-danger" @click="click">
        <slot>{{ $t('delete') }}</slot>
        <i class="fas fa-trash" style="margin-left: 5px;"></i>
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
