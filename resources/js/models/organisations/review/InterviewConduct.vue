<template>
    <div class="modal fade col-12 full-width" id="interviewConductModal" tabindex="-1" aria-labelledby="interviewConductModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ activeCollection?.messages?.interview }} {{ activeCollection?.messages?.conduct }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="modalHide"></button>
                </div>
                <!--- Interviews Table-->
                <div class="row">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ activeCollection?.messages?.interviews }}</h4>
                            </div>
                            <div class="table">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>{{ activeCollection?.messages?.creator }}</th>
                                            <th>{{ activeCollection?.messages?.interviewee }}</th>
                                            <th>{{ activeCollection?.messages?.status }}</th>
                                            <th class="text-center">{{ activeCollection?.messages?.actions }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="interview in interviews" :key="interview">
                                            <td>
                                                <span class="fw-bold">{{ interview.id }}</span>
                                            </td>
                                            <td>
                                                {{ interview.creator?.name }}
                                            </td>
                                            <td>
                                                {{ interview.interviewee }}
                                            </td>
                                            <td>
                                                {{ getStatusLabel(interview.status) }}
                                            </td>
                                            <td class="text-center">
                                                <button type="button" :class="`btn btn-relief-${interview.id == interviewExpanded.id ? 'warning' : 'dark'}`" @click="interviewExpandedSet(interview.id)">
                                                    {{ interview.id == interviewExpanded.id ? activeCollection?.messages?.expanded : activeCollection?.messages?.expand }}
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Accordion for Interview Statements -->
                        <div class="accordion mt-3" :id="`interviewStatementsAccordion${interviewExpanded.id}`">
                            <div v-for="statement in interviewExpanded.statements" :key="statement" class="card accordion-item">
                                <!-- Statement Latest Review Status -->
                                <div class="row px-1">
                                    <div class="col-8"></div>
                                    <div class="col-4 d-flex justify-content-end align-items-center">
                                        <span :class="`badge rounded-pill badge-glow bg-${statement.latestReview?.class}`" style="height: 1.5rem">{{ statement.latestReview?.review_status }}</span>
                                    </div>
                                </div>
                                <!-- Accordion Title (Statement Content)-->
                                <h2 class="accordion-header" :id="`interviewStatementHeader${statement.id}`">
                                    <button class="accordion-button collapsed" data-bs-toggle="collapse" role="button" :data-bs-target="`#interviewStatement${statement.id}`" aria-expanded="false" :aria-controls="`interviewStatement${statement.id}`">
                                        {{ statement["content_" + locale] }}
                                    </button>
                                </h2>
                                <div :id="`interviewStatement${statement.id}`" class="collapse accordion-collapse" :aria-labelledby="`interviewStatementHeader${statement.id}`" :data-bs-parent="`#interviewStatementsAccordion${interviewExpanded.id}`">
                                    <!-- Expanded statement desc-->
                                    <div class="accordion-body">
                                        {{ statement["desc_" + locale] }}
                                    </div>
                                    <!-- Value Card-->
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="text-uppercase">{{ activeCollection?.messages?.value }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <div :id="`interviewStatementValueSlider${interviewExpanded.id}_${statement.id}`" class="mt-1 mb-3"></div>
                                            <div class="mb-3">
                                                <label for="interviewReviewText{{ interviewExpanded.id }}_{{ statement.id }}" class="form-label">{{ activeCollection?.messages?.review }}</label>
                                                <textarea class="form-control" :id="`interviewReviewText${interviewExpanded.id}_${statement.id}`" :name="`interviewReviewText${interviewExpanded.id}_${statement.id}`" rows="4"></textarea>
                                            </div>
                                            <!-- Value Action Buttons (Accept/Reject)-->
                                            <div class="mb-3">
                                                <button type="button" class="btn btn-success w-25 me-2" @click="reviewUpdate('accept', statement.id, `interviewReviewText${interviewExpanded.id}_${statement.id}`, `interviewStatementValueSlider${interviewExpanded.id}_${statement.id}`, statement.latestDeed?.id)">
                                                    {{ activeCollection?.messages?.accept }}
                                                </button>
                                                <button type="button" class="btn btn-danger w-25" @click="reviewUpdate('reject', statement.id, `interviewReviewText${interviewExpanded.id}_${statement.id}`, `interviewStatementValueSlider${interviewExpanded.id}_${statement.id}`, statement.latestDeed?.id)">
                                                    {{ activeCollection?.messages?.reject }}
                                                </button>
                                            </div>
                                            <!-- Last Updated -->
                                            <p>{{ activeCollection?.messages?.lastUpdated }}: {{ statement.latestDeed?.lastUpdated }}, {{ activeCollection?.messages?.by }}: {{ statement.latestDeed?.user }}</p>
                                            <!-- Last Review Comment -->
                                            <p>{{ activeCollection?.messages?.comment }}: {{ statement.latestDeed?.comment }}</p>
                                            <!-- Latest Review Details-->
                                            <p>{{ activeCollection?.messages?.latestReview }}, {{ statement.latestReview?.user }}, {{ statement.latestReview?.lastUpdated }}: {{ statement.latestReview?.review }}</p>
                                            <p :class="`text-${statement.latestReview?.class}`">{{ statement.latestReview?.review_status }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Interview Details -->
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ activeCollection?.messages?.agenda }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="border p-3 rounded mb-3">
                                    {{ interviewExpanded?.agenda }}
                                </div>

                                <!-- Status Management -->
                                <div class="mb-3">
                                    <h5>{{ activeCollection?.messages?.updateStatus }}</h5>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-secondary" :class="{ 'active': interviewExpanded?.status === 'planned' }" @click="updateInterviewStatus('planned')">
                                            {{ activeCollection?.messages?.planned }}
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" :class="{ 'active': interviewExpanded?.status === 'in_progress' }" @click="updateInterviewStatus('in_progress')">
                                            {{ activeCollection?.messages?.inProgress }}
                                        </button>
                                        <button type="button" class="btn btn-outline-success" :class="{ 'active': interviewExpanded?.status === 'completed' }" @click="updateInterviewStatus('completed')">
                                            {{ activeCollection?.messages?.completed }}
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" :class="{ 'active': interviewExpanded?.status === 'cancelled' }" @click="updateInterviewStatus('cancelled')">
                                            {{ activeCollection?.messages?.cancelled }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Notes Section -->
                                <div class="mb-3">
                                    <label for="interviewNotes" class="form-label">{{ activeCollection?.messages?.notes }}</label>
                                    <textarea class="form-control" id="interviewNotes" v-model="interviewNotes" rows="4" :placeholder="activeCollection?.messages?.addNotesHere"></textarea>
                                    <button type="button" class="btn btn-primary mt-2" @click="saveNotes">{{ activeCollection?.messages?.saveNotes }}</button>
                                </div>

                                <!-- File Upload Section -->
                                <div class="mb-3">
                                    <label for="interviewFile" class="form-label">{{ activeCollection?.messages?.uploadFile }}</label>
                                    <input type="file" class="form-control" id="interviewFile" ref="fileInput" @change="handleFileUpload">
                                    <button type="button" class="btn btn-info mt-2" @click="uploadFile" :disabled="!selectedFile">{{ activeCollection?.messages?.upload }}</button>
                                </div>

                                <!-- Attachments List -->
                                <div v-if="getAttachments().length > 0" class="mb-3">
                                    <h5>{{ activeCollection?.messages?.attachments }}</h5>
                                    <ul class="list-group">
                                        <li v-for="(attachment, index) in getAttachments()" :key="index" class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ attachment.filename }}</span>
                                            <div>
                                                <a :href="getAttachmentUrl(attachment.path)" target="_blank" class="btn btn-sm btn-outline-primary me-2">{{ activeCollection?.messages?.download }}</a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" @click="deleteAttachment(index)">{{ activeCollection?.messages?.delete }}</button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "InterviewConduct",
    props: ["actionId", "collection", "locale", "org"],
    data() {
        return {
            interviews: [],
            interviewExpanded: {},
            interviewStatements: [], // ?
            interviewNotes: '',
            selectedFile: null,
            localCollection: null
        }
    },
    computed: {
        activeCollection() {
            return this.localCollection || this.collection;
        }
    },
    methods: {
        interviewConduct() {
            // refresh the collection in case some changes has been commited by another modal
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/review/" + thisComponent.actionId, {})
                .then(function (response) {
                    console.log(response.data);
                    // Store collection locally to ensure messages are available
                    thisComponent.localCollection = response.data;
                    thisComponent.interviews = response.data.statistics?.statements?.interview?.interviews;
                    if (thisComponent.interviews.length > 0) {
                        thisComponent.interviewExpanded = thisComponent.interviews[0];
                        thisComponent.$nextTick(() => {
                            thisComponent.interviewExpandedBuildSliders();
                        });
                    }
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
            this.$nextTick(() => {
                $("#interviewConductModal").modal("show");
            });
        },
        interviewExpandedBuildSliders() {
            var thisComponent = this;
            thisComponent.$nextTick(() => {
                // create sliders for mounted interview statements
                let slider = null;
                thisComponent.interviewExpanded.statements.forEach((s) => {
                    slider = document.getElementById(`interviewStatementValueSlider${thisComponent.interviewExpanded.id}_${s.id}`);
                    //console.log(slider);
                    noUiSlider.create(slider, {
                        start: s.latestDeed.value,
                        step: 1,
                        range: {
                            min: 1,
                            max: 5,
                        },
                        tooltips: true,
                        direction: "ltr",
                        pips: {
                            mode: "steps",
                            stepped: false,
                            density: 1,
                        },
                    });
                });
            });
        },
        interviewExpandedSet(id) {
            var thisComponent = this;
            let i = this.interviews.filter(x => {
                return x.id == id;
            });
            i = i[0];
            if (i) {
                this.interviewExpanded = i;
                this.interviewNotes = i.notes || '';
                this.$nextTick(() => {
                    thisComponent.interviewExpandedBuildSliders();
                })
            };

        },
        getStatusColor(status) {
            const colors = {
                'planned': 'secondary',
                'in_progress': 'primary',
                'completed': 'success',
                'cancelled': 'danger'
            };
            return colors[status] || 'secondary';
        },
        getStatusLabel(status) {
            const labels = {
                'planned': this.activeCollection?.messages?.planned,
                'in_progress': this.activeCollection?.messages?.inProgress,
                'completed': this.activeCollection?.messages?.completed,
                'cancelled': this.activeCollection?.messages?.cancelled
            };
            return labels[status] || status;
        },
        updateInterviewStatus(status) {
            var thisComponent = this;
            axios
                .post(`/${this.locale}/interviews/${this.interviewExpanded.id}/status`, {
                    status: status
                })
                .then(function (response) {
                    thisComponent.interviewExpanded.status = status;
                    thisComponent.rebuild();
                    toastr["success"]("👋 " + thisComponent.activeCollection?.messages?.statusUpdated, "Success", {
                        closeButton: true,
                        tapToDismiss: false,
                        progressBar: true,
                        rtl: false,
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                    toastr["error"](`👋 ${error.response?.data?.message || 'Error'}`, "Error!", {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                    });
                });
        },
        saveNotes() {
            var thisComponent = this;
            axios
                .post(`/${this.locale}/interviews/${this.interviewExpanded.id}/notes`, {
                    notes: this.interviewNotes
                })
                .then(function (response) {
                    thisComponent.interviewExpanded.notes = thisComponent.interviewNotes;
                    toastr["success"]("👋 " + thisComponent.activeCollection?.messages?.notesSaved, "Success", {
                        closeButton: true,
                        tapToDismiss: false,
                        progressBar: true,
                        rtl: false,
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                    toastr["error"](`👋 ${error.response?.data?.message || 'Error'}`, "Error!", {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                    });
                });
        },
        handleFileUpload(event) {
            this.selectedFile = event.target.files[0];
        },
        uploadFile() {
            if (!this.selectedFile) return;

            var thisComponent = this;
            const formData = new FormData();
            formData.append('file', this.selectedFile);

            axios
                .post(`/${this.locale}/interviews/${this.interviewExpanded.id}/upload`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(function (response) {
                    thisComponent.selectedFile = null;
                    thisComponent.$refs.fileInput.value = '';
                    thisComponent.rebuild();
                    toastr["success"]("👋 " + thisComponent.activeCollection?.messages?.fileUploaded, "Success", {
                        closeButton: true,
                        tapToDismiss: false,
                        progressBar: true,
                        rtl: false,
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                    toastr["error"](`👋 ${error.response?.data?.message || 'Error'}`, "Error!", {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                    });
                });
        },
        getAttachments() {
            if (!this.interviewExpanded?.attachments) return [];
            try {
                return JSON.parse(this.interviewExpanded.attachments);
            } catch (e) {
                return [];
            }
        },
        getAttachmentUrl(path) {
            // Extract filename from path
            const filename = path.split('/').pop();
            return `/${this.locale}/interviews/${this.interviewExpanded.id}/download/${filename}`;
        },
        deleteAttachment(index) {
            var thisComponent = this;
            const attachments = this.getAttachments();
            const attachment = attachments[index];

            if (!confirm(`Delete ${attachment.filename}?`)) {
                return;
            }

            // Extract filename from path
            const filename = attachment.path.split('/').pop();

            // URL encode the filename to handle spaces and special characters
            const encodedFilename = encodeURIComponent(filename);

            axios
                .delete(`/${this.locale}/interviews/${this.interviewExpanded.id}/delete/${encodedFilename}`)
                .then(function (response) {
                    thisComponent.rebuild();
                    toastr["success"]("👋 " + thisComponent.activeCollection?.messages?.fileDeleted, "Success", {
                        closeButton: true,
                        tapToDismiss: false,
                        progressBar: true,
                        rtl: false,
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                    toastr["error"](`👋 ${error.response?.data?.message || 'Error'}`, "Error!", {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                    });
                });
        },
        modalHide() {
            this.$parent.rebuild();
            this.$nextTick(() => {
                $("#interviewConductModal").modal("hide");
            });
        },
        rebuild() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/review/" + thisComponent.actionId, {}) 
                .then(function (response) {
                    //console.log(response.data);
                    thisComponent.interviews = response.data.statistics?.statements?.interview?.interviews;
                    if (thisComponent.interviews.length > 0) {
                        // do we have an expanded already
                        if (thisComponent.interviewExpanded) {
                            // find expanded from interviews
                            let ex = thisComponent.interviews.filter(i => {
                                return i.id == thisComponent.interviewExpanded.id
                            });
                            ex = ex[0];
                            thisComponent.interviewExpanded = ex;
                        } else {
                            thisComponent.interviewExpanded = thisComponent.interviews[0];
                        }
                    };
                    // clear inputs
                    const elements = document.querySelectorAll('[id^="interviewReviewText"]');
                    // Loop through the elements and set their value to null (empty string)
                    elements.forEach(element => {
                        element.value = '';
                    });
                    // rebuild sliders
                    thisComponent.interviewExpandedBuildSliders();
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
        },
        reviewUpdate(kind, statementId, inputName, sli, deedId) {
            var thisComponent = this;
            // init with an accept
            let reviewStatusId = 2;
            if (kind == "accept") {
                reviewStatusId = 2;
            } else {
                reviewStatusId = 3;
            }
            // get the review text
            let rev = document.getElementById(inputName).value;
            // get the updated value
            let sliderValue = document.getElementById(sli).noUiSlider.get();
            axios
                .post(`/${this.locale}/axios/organisations/${this.org}/review/conduct-update`, {
                    review_status_id: reviewStatusId,
                    review: rev,
                    statement_id: statementId,
                    value: sliderValue,
                    deed_id: deedId,
                })
                .then(function (response) {
                    //console.log(response.data);
                    thisComponent.rebuild();
                    toastr["success"]("👋 Updated!.", "Success", {
                        closeButton: true,
                        tapToDismiss: false,
                        progressBar: true,
                        rtl: false,
                    });
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                    toastr["error"](`👋 ${error.response?.data}`, "Error!", {
                        closeButton: true,
                        tapToDismiss: false,
                        rtl: false,
                    });
                });
        },
    },
}
</script>