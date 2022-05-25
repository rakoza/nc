<template>
    <div>
        <!-- Header -->
        <page-header :title="$tc('tenant', 2)">
            <!-- Buttons -->
            <div class="level-item buttons">
                <b-button
                    @click="$emit('create-tenant')"
                    type="is-primary"
                    icon-pack="fas"
                    icon-right="user-plus">
                    {{ $t('create') }}
                </b-button>
            </div>
        </page-header>

        <section class="section">
            <div class="table-container">
                <table class="table is-fullwidth is-hoverable">
                    <thead>
                        <th style="width: 30px;"></th>
                        <th style="width: 200px;">{{ $t('domain') }}</th>
                        <th style="width: 250px;">{{ $t('name') }}</th>
                        <th style="width: 250px;">{{ $t('email_address') }}</th>
                        <th>{{ $t('notes') }}</th>
                        <th class="has-text-right">{{ $t('trial_period_end_date') }}</th>
                        <th style="width: 90px;" class="has-text-right">{{ $t('is_active') }}</th>
                        <th style="width: 150px;"></th>
                    </thead>

                    <tbody>
                        <tr v-for="(item,index) in tenants" :key="index">
                            <td>{{ index + 1 }}.</td>
                            <td>
                                <a :href="'http://' + item.domain" target="_blank">
                                    {{ item.domain }}
                                    <i class="fas fa-external-link"></i>
                                </a>
                            </td>
                            <td>{{ item.name }}</td>
                            <td>{{ item.email }}</td>
                            <td>{{ item.notes }}</td>
                            <td class="has-text-right">{{ item.trial_period_end_date | dd_mm_yyyy }}</td>
                            <td class="has-text-right"
                                :class="{'has-text-danger has-text-weight-bold': !item.is_active}"
                                >
                                {{ item.is_active ? 'Da' : 'Ne' }}
                            </td>
                            <td class="py-1">
                                <div class="is-flex is-flex-wrap-nowrap is-justify-content-flex-end">
                                    <!-- Edit -->
                                    <b-button
                                        size="is-small is-rounded is-lowercase"
                                        @click="$emit('show-tenant', item)"
                                        type="is-light"
                                        icon-pack="fas"
                                        icon-right="link">
                                        {{ $t('details') }}
                                    </b-button>
                                    <!-- Edit -->
                                    <b-button
                                        size="is-small is-rounded is-lowercase"
                                        @click="$emit('edit-tenant', item)"
                                        type="is-primary is-light"
                                        icon-pack="fas"
                                        icon-right="edit">
                                        {{ $t('edit') }}
                                    </b-button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>

<script>

export default {
    props: ['tenants'],
}
</script>
