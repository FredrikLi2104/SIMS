<template>
    <div class="row">
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="question-en">{{ `${messages.question} ${messages.inEnglish}` }}</label>
                <input type="text" id="question-en" :class="`form-control ${errors?.question_en ? 'is-invalid' : ''}`"
                       name="question_en" placeholder="" v-model="questionEn"/>
                <div v-if="errors?.question_en" class="invalid-feedback">{{ errors.question_en[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="question-se">{{ `${messages.question} ${messages.inSwedish}` }}</label>
                <input type="text" id="question-se" :class="`form-control ${errors?.question_se ? 'is-invalid' : ''}`"
                       name="question_se" placeholder="" v-model="questionSe"/>
                <div v-if="errors?.question_se" class="invalid-feedback">{{ errors.question_se[0] }}</div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="mb-1">
                <label class="form-label col-form-label-sm"
                       for="sort-order">{{ messages.sortOrder }}</label>
                <input type="text" id="sort-order"
                       :class="`form-control ${errors?.sort_order ? 'is-invalid' : ''}`"
                       :placeholder="sortOrder" name="sort_order" v-model="faqSortOrder">
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
                <label class="form-label" for="answer-en">{{ `${messages.answer} ${messages.inEnglish}` }}</label>
                <div id="answer-en-quill"></div>
                <input id="answer-en" :class="`${errors?.answer_en ? 'is-invalid' : ''}`" name="answer_en" type="hidden"
                       :value="answerEn" hidden/>
                <div v-if="errors?.answer_en" class="invalid-feedback">{{ errors.answer_en[0] }}</div>
            </div>
        </div>
        <div class="col-12">
            <div class="mb-1">
                <label class="form-label" for="answer-se">{{ `${messages.answer} ${messages.inSwedish}` }}</label>
                <div id="answer-se-quill"></div>
                <input id="answer-se" :class="`${errors?.answer_se ? 'is-invalid' : ''}`" name="answer_se" type="hidden"
                       :value="answerSe" hidden/>
                <div v-if="errors?.answer_se" class="invalid-feedback">{{ errors.answer_se[0] }}</div>
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
    name: "Faqs",
    props: ['locale', 'messages', 'isUpdate', 'faqData', 'sortOrder'],
    data() {
        return {
            errors: null,
            faqSortOrder: this.sortOrder,
            questionEn: this.faqData?.question_en,
            questionSe: this.faqData?.question_se,
            answerEn: this.faqData?.answer_en,
            answerSe: this.faqData?.answer_se,
            live: this.faqData?.live,
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
        initAnswerEnQuill() {
            const self = this;
            let answerEnQuill = new Quill('#answer-en-quill', self.quillOptions());

            try {
                let oldDelta = JSON.parse(self.answerEn);
                answerEnQuill.setContents(oldDelta);
            } catch (error) {

            }

            answerEnQuill.on('text-change', function () {
                self.answerEn = JSON.stringify(answerEnQuill.getContents());
                document.getElementById('answer-en').value = self.delta;
            });
        },
        initAnswerSeQuill() {
            const self = this;
            let answerSeQuill = new Quill('#answer-se-quill', self.quillOptions());

            try {
                let oldDelta = JSON.parse(self.answerSe);
                answerSeQuill.setContents(oldDelta);
            } catch (error) {

            }

            answerSeQuill.on('text-change', function () {
                self.answerSe = JSON.stringify(answerSeQuill.getContents());
                document.getElementById('answer-se').value = self.delta;
            });
        },
        submit() {
            let self = this;
            let url = `/${self.locale}/faqs`;
            let formData = new FormData($('#form')[0]);
            let res = null;
            self.isSubmitting = true;

            if (self.isUpdate) {
                res = axios.post(`${url}/${self.faqData.id}`, formData);
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
        this.initAnswerEnQuill();
        this.initAnswerSeQuill();
    }
}
</script>

<style scoped>

</style>
