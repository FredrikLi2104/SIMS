<template>
    <div class="row">
        <div class="col-12">
            <div v-show="faqs.length" class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i data-feather="help-circle" class="card-header-icon"></i>
                        <h4 class="card-title">{{ messages.faqs }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-50">
                        <div class="col-12">
                            <div id="faqs-accordion" class="accordion accordion-margin" data-toggle-hover="true">
                                <template v-for="(faq, index) in faqs">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" :id="`faq-heading-${index}`">
                                            <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse" :data-bs-target="`#link-${index}`">
                                                {{ faq[`question_${locale}`] }}
                                            </button>
                                        </h2>
                                        <div :id="`link-${index}`"
                                             :class="`accordion-collapse collapse ${index === 0 ? 'show' : ''}`"
                                             data-bs-parent="#faqs-accordion">
                                            <div class="accordion-body"
                                                 v-html="deltaToHtml(faq[`answer_${locale}`])"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-show="links.length" class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <i data-feather="external-link" class="card-header-icon"></i>
                        <h4 class="card-title">{{ messages.links }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-50">
                        <div class="col-12" v-for="link in links">
                            <div v-html="deltaToHtml(link[`content_${locale}`])"
                                 class="bg-light-secondary rounded p-1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {QuillDeltaToHtmlConverter} from 'quill-delta-to-html';

export default {
    name: "OrganisationCheck",
    props: ['locale', 'messages', 'faqs', 'links'],
    methods: {
        deltaToHtml(delta) {
            let deltaOps = [];

            try {
                deltaOps = JSON.parse(delta).ops;
            } catch (error) {

            }

            let converter = new QuillDeltaToHtmlConverter(deltaOps, {});
            return converter.convert();
        }
    }
}
</script>

<style scoped>
.card .card-header-icon {
    width: 1.714rem;
    height: 1.714rem;
    margin-right: 0.5rem;
}

.bg-light-secondary:deep(p:last-child) {
    margin-bottom: 0;
}
</style>
