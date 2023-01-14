<template>
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="sort-order">{{ messages.sortOrder }}</label>
                <input type="text" id="sort-order"
                       :class="`form-control ${errors?.sort_order ? 'is-invalid' : ''}`"
                       :placeholder="sortOrder" name="sort_order" v-model="sortOrder">
                <div v-if="errors?.sort_order" class="invalid-feedback">{{ errors.sort_order[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm form-check-label mb-50"
                       for="live">Live</label>
                <div class="form-check form-switch form-check-success">
                    <input type="checkbox" class="form-check-input" id="live" name="live"
                           value="1" :checked="live"/>
                    <label class="form-check-label" for="live">
                        <span class="switch-icon-left"><i data-feather="check"></i></span>
                        <span class="switch-icon-right"><i data-feather="x"></i></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="mb-1">
                <label class="form-label" for="content-en">{{ messages.content_en }}</label>
                <div id="content-en-quill"></div>
                <input id="content-en" :class="`${errors?.content_en ? 'is-invalid' : ''}`" name="content_en"
                       type="hidden" :value="contentEn" hidden/>
                <div v-if="errors?.content_en" class="invalid-feedback">{{ errors.content_en[0] }}</div>
            </div>
        </div>
        <div class="col-12">
            <div class="mb-1">
                <label class="form-label" for="content-se">{{ messages.content_se }}</label>
                <div id="content-se-quill"></div>
                <input id="content-se" :class="`${errors?.content_se ? 'is-invalid' : ''}`" name="content_se"
                       type="hidden" :value="contentSe" hidden/>
                <div v-if="errors?.content_se" class="invalid-feedback">{{ errors.content_se[0] }}</div>
            </div>
        </div>
        <div class="col-12">
            <button type="button" class="btn btn-primary waves-effect waves-float waves-light" :disabled="isSubmitting"
                    @click="submit">
                <span v-show="!isSubmitting">{{ messages.submit }}</span>
                <span v-show="isSubmitting" class="spinner-border spinner-border-sm" role="status"
                      aria-hidden="true"></span>
                <span v-show="isSubmitting" class="ms-25 align-middle">{{ messages.submitting }}...</span>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "Links",
    props: ['locale', 'messages', 'isUpdate', 'linkData', 'sortOrder'],
    data() {
        return {
            errors: null,
            contentEn: this.linkData?.content_en,
            contentSe: this.linkData?.content_se,
            live: this.linkData?.live,
            isSubmitting: false
        }
    },
    methods: {
        quillOptions() {
            return {
                modules: {
                    formula: true,
                    syntax: true,
                    toolbar: [
                        [
                            {
                                font: [],
                            },
                            {
                                size: [],
                            },
                        ],
                        ["bold", "italic", "underline", "strike"],
                        [
                            {
                                color: [],
                            },
                            {
                                background: [],
                            },
                        ],
                        [
                            {
                                script: "super",
                            },
                            {
                                script: "sub",
                            },
                        ],
                        [
                            {
                                header: "1",
                            },
                            {
                                header: "2",
                            },
                            "blockquote",
                            "code-block",
                        ],
                        [
                            {
                                list: "ordered",
                            },
                            {
                                list: "bullet",
                            },
                            {
                                indent: "-1",
                            },
                            {
                                indent: "+1",
                            },
                        ],
                        [
                            "direction",
                            {
                                align: [],
                            },
                        ],
                        ["link", "image", "video", "formula"],
                        ["clean"],
                    ],
                },
                theme: "snow",
            };
        },
        initContentEnQuill() {
            const self = this;
            let contentEnQuill = new Quill('#content-en-quill', self.quillOptions());

            try {
                let oldDelta = JSON.parse(self.contentEn);
                contentEnQuill.setContents(oldDelta);
            } catch (error) {

            }

            contentEnQuill.on('text-change', function () {
                self.contentEn = JSON.stringify(contentEnQuill.getContents());
                document.getElementById('content-en').value = self.delta;
            });
        },
        initContentSeQuill() {
            const self = this;
            let contentSeQuill = new Quill('#content-se-quill', self.quillOptions());

            try {
                let oldDelta = JSON.parse(self.contentSe);
                contentSeQuill.setContents(oldDelta);
            } catch (error) {

            }

            contentSeQuill.on('text-change', function () {
                self.contentSe = JSON.stringify(contentSeQuill.getContents());
                document.getElementById('content-se').value = self.delta;
            });
        },
        submit() {
            let self = this;
            let url = `/${self.locale}/links`;
            let formData = new FormData($('#form')[0]);
            let res = null;
            self.isSubmitting = true;

            if (self.isUpdate) {
                res = axios.post(`${url}/${self.linkData.id}`, formData);
            } else {
                res = axios.post(url, formData);
            }

            res.then(function (response) {
                if (response.data.success) {
                    window.location.href = url;
                } else {
                    self.isSubmitting = false;

                    toastr["error"](response.data.msg, self.messages?.error, {
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 3000,
                        progressBar: true,
                        "positionClass": "toast-top-center",
                    });
                }
            }).catch(function (error) {
                self.isSubmitting = false;
                self.errors = error.response?.data?.errors;
                console.log(error);
                console.log(error.response);
            });
        }
    },
    mounted() {
        this.initContentEnQuill();
        this.initContentSeQuill();
    }
}
</script>

<style scoped>

</style>
