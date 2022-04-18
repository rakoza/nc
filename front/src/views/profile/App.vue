<template>
    <div>
        <!-- Header -->
        <page-header :title="title">
            <!-- Buttons -->
            <div class="level-item buttons">
                <!-- Promjena podataka -->
                <b-button
                    @click="deleteAccount"
                    :loading="isLoadingDelete"
                    type="is-light has-text-danger"
                    icon-pack="fas"
                    icon-right="user-slash">
                    {{ $t('delete_account') }}
                </b-button>

                <!-- Promjena lozinke -->
                <b-button
                    @click="changePassword"
                    type="is-primary"
                    icon-pack="fas"
                    icon-right="key">
                    {{ $t('change_password') }}
                </b-button>

            </div>
        </page-header>

        <!-- Container -->
        <section class="section">
            <!-- column 1 -->
            <div class="columns">
                <div class="column is-one-quarter has-text-centered">
                    <div v-if="profile" class="px-5">
                        <div class="photo-border" @click="openCropperModal()">
                            <!-- <img :src="profile.media[0].original_url" alt="profile" v-if="profile.media[0]">
                            <img src="/assets/img/client-logo/male.jpg" alt="profile" v-else> -->
                            <img src="../../assets/face.svg" width="200" height="200" alt="profile">
                        </div>
                        <div v-if="isUpLoading">
                            <b-icon
                                pack="fas"
                                icon="sync-alt"
                                custom-class="fa-spin"
                                class="mr-2">
                            </b-icon>
                            uploading...
                        </div>

                        <!-- <button
                            class="button is-size-7 mt-2 is-fullwidth"
                            v-if="profile.media[0]"
                            @click="deleteImage">
                            <i class="fas fa-trash mr-2"></i>
                            Delete
                        </button> -->
                    </div>
                </div>
                <div class="column">
                    <!-- name -->
                    <b-field
                        class="required"
                        :label="$t('full_name')"
                        :type="form.hasError('name')"
                        :message="form.errorMessage('name')">
                        <b-input
                            size="is-large"
                            name="name"
                            v-model="form.name"
                            :placeholder="$t('full_name')">
                        </b-input>
                    </b-field>

                    <!-- email -->
                    <b-field :label="$t('email_address')">
                        <b-input
                            v-if="profile"
                            disabled
                            icon="at"
                            v-model="profile.email"
                            name="email"
                            placeholder="Email">
                        </b-input>
                    </b-field>

                    <!-- phone -->
                    <b-field
                        :label="$t('telephone_number')"
                        :type="form.hasError('phone')"
                        :message="form.errorMessage('phone')">
                        <b-input
                            icon="phone"
                            v-model="form.phone"
                            name="phone"
                            :placeholder="$t('telephone_number')">
                        </b-input>
                    </b-field>

                    <!-- address -->
                    <b-field
                        :label="$t('full_address')"
                        :type="form.hasError('address')"
                        :message="form.errorMessage('address')">
                        <b-input
                            icon="map"
                            v-model="form.address"
                            name="address"
                            :placeholder="$t('full_address')">
                        </b-input>
                    </b-field>

                    <!-- notes -->
                    <b-field
                        :label="$t('write_something_about_yourself')"
                        :type="form.hasError('notes')"
                        :message="form.errorMessage('notes')">
                        <b-input
                            type="textarea"
                            maxlength="1000"
                            v-model="form.notes"
                            name="notes"
                            :placeholder="$t('write_something_about_yourself')">
                        </b-input>
                    </b-field>

                    <hr>
                    <modal-button-save @click="submit()"></modal-button-save>
                </div>
            </div>
        </section>

    </div>
</template>

<script>

import FormPassword from './_form_change_password.vue'
// import ImageCropper from '@/components/ImageCropper'

export default {
    data() {
        let item = {
            name: '',
            phone: '',
            address: '',
            notes: '',
        };

        return {
            profile: null,
            form: new this.$form(item),

            isLoadingDelete: false,
            isUpLoading: false,
        }
    },

    computed: {
        title() {
            return this.$t('profile')
        }
    },

    created() {
        this.reloadData()
    },

    methods: {
        reloadData()
        {
            this.$api.profile.get()
                .then(data => {
                    this.form.name = data.name
                    this.form.phone = data.phone
                    this.form.address = data.address
                    this.form.notes = data.notes

                    this.profile = data
                })
                .catch(error => {
                    this.$alertError(error.message);
                    throw error
                })
                .finally(() => {
                    // this.isLoading = false
                });
        },
        submit()
        {
            this.form
                .put('/profile')
                .then(() => {
                    this.$notify('Promjenili ste podatke', 'is-success')

                    this.$store.dispatch('auth/me')
                        .then(() => {
                            console.log('all good')
                        })
                        .catch(error => this.$alertError(error))

                })
                .catch(() => {})
        },
        deleteAccount()
        {
            const runDeleteAccount = () => {

                this.isLoadingDelete = true

                this.$api.profile.delete()
                    .then(() => {
                        // account deleted
                        // ovdje trebam izbaciti poruku 3sec
                        // i redirectovati korisnika na site
                        this.$buefy.toast.open({
                            duration: 3000,
                            message: 'Nalog je izbrisan',
                            type: 'is-danger',
                            queue: false
                        })

                        const redirectToWebSite = () => {
                            setTimeout(function() {
                                window.location = '/'
                            }, 2000)
                        }

                        redirectToWebSite()
                    })
                    .catch(error => {
                        this.$alertError(error.message);
                        // throw error
                    })
                    .finally(() => {
                        this.isLoadingDelete = false
                    });
            }

            this.$buefy.dialog.confirm({
                title: this.$t('delete_account'),
                message: this.$t('delete_account_confirmation_statement'),
                confirmText: this.$t('yes_delete_account'),
                cancelText: this.$t('cancel'),
                type: 'is-danger',
                hasIcon: true,
                onConfirm: () => runDeleteAccount()
            })
        },
        changePassword()
        {
            this.$buefy.modal.open({
                parent: this,
                component: FormPassword,
                hasModalCard: true,
            });
        },
        openCropperModal()
        {
            // const media = this.profile.media[0]

            // this.$buefy.modal.open({
            //     parent: this,
            //     component: ImageCropper,
            //     hasModalCard: true,
            //     props: {
            //         label: 'Profile photo',
            //         imageUrl: media ? media.original_url : null,
            //         aspectRatio: 1,
            //         type: 'png', // uvijek snima kao png, ne znam zasto
            //     }, // Props to be binded to the injected component
            //     events: {
            //         'data-saved': this.fileSelected,
            //     }, // Events to be binded to the injected component
            // });
        },
        fileSelected(file)
        {
            console.log(file)
            // this.isUpLoading = true

            // const formData = new FormData()
            // formData.append('file', file)

            // this.$api.media.uploadProfilePhoto(formData)
            //     .then(fileInfo => {
            //         // this.form.attachments.push(fileInfo)
            //         // this.file = null
            //         console.log(fileInfo);
            //         this.reloadData()
            //     })
            //     .catch(error => this.$alertError(error.message))
            //     .finally(() => {
            //         this.isUpLoading = false
            //     });
        },
        deleteImage()
        {
            // this.$buefy.dialog.confirm({
            //     title: 'Potvrdi akciju',
            //     message: 'Da li ste sigurni da želite izbrisati fotografiju?',
            //     confirmText: 'Da',
            //     cancelText: 'Ne',
            //     type: 'is-danger',
            //     hasIcon: true,
            //     onConfirm: () => {
            //         this.$api.media.deleteProfilePhoto()
            //             .then(() => {
            //                 this.$notify('Izbrisali ste fotografiju', 'is-success')
            //                 this.reloadData()
            //             })
            //             .catch(error => this.$alertError(error.message))
            //             .finally(() => {
            //                 // this.isLoading = false
            //             });
            //     }
            // })
        },
    },
};
</script>

<style scoped>
.photo-border {
    cursor: pointer;
}
.photo-border img {
    object-fit: contain;
    border-radius: 5px;
    border: 1px dotted grey;
    padding: 3px;
}
.photo-border img:hover {
    border: 1px solid grey;
}
</style>
