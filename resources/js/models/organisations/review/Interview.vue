<template>
    <div class="modal fade col-12 full-width" id="interviewModal" tabindex="-1" aria-labelledby="interviewModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ collection?.messages?.interview }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="interviewHide"></button>
                </div>
                <div class="modal-body">
                    <div v-if="conductReady" class="avatar bg-light-success rounded full-width mb-1">
                        <p class="text-center full-width mt-0 mb-0 py-2">{{ collection?.messages?.readyToConduct }}</p>
                    </div>
                    <p>{{ collection?.messages.interview }}</p>

                    <!-- Existing Interviews Pills-->
                    <div class="row mb-3" v-if="interviews.length > 0">
                        <div class="col-12">
                            <p class="mb-1"><strong>{{ collection?.messages.interviews }}</strong></p>
                            <ul class="nav nav-pills mb-2">
                                <li class="nav-item" v-for="interview in interviews" :key="interview" @click="existingSetActive(interview.id)">
                                    <a :class="`nav-link ${existingActive.id == interview.id ? 'active' : ''}`" style="cursor: pointer;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        <span class="fw-bold">{{ collection?.messages?.interview }} {{ interview.id }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="px-2 py-1">
                    <div class="row">
                        <!-- Input Details-->
                        <div class="col-6" style="flex: 0 0 50%; min-width: 50%">
                            <div class="mb-1">
                                <label class="form-label" for="user">
                                    {{ collection?.messages?.interviewee }} {{ collection?.messages?.email }}
                                </label>
                                <input type="email" :class="`form-control ${interviewCreateErrors.user ? 'is-invalid' : ''}`" id="interviewCreateUser" placeholder="john.doe@company.com" />
                                <div class="invalid-feedback">{{ interviewCreateErrors.user?.message }}</div>
                                <small class="form-text text-muted">{{ collection?.messages?.enterEmailForCalendarInvitation || 'Enter email address to receive calendar invitation' }}</small>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="agenda">{{ collection?.messages?.agenda }}</label>
                                <textarea :class="`form-control ${interviewCreateErrors.agenda ? 'is-invalid' : ''}`" id="agenda" rows="3" placeholder="Interview Agenda..."></textarea>
                                <div class="invalid-feedback">{{ interviewCreateErrors.agenda?.message }}</div>
                            </div>
                            <div class="scrollable-container mb-1" @dragover.prevent @drop="dragReceive">
                                <div :class="`inner-content ${interviewCreateErrors.statements ? 'is-invalid' : ''}`">
                                    <div v-for="createStatement in toCreate" :key="createStatement" class="d-flex justify-content-between align-items-center" style="background: rgba(115, 103, 240, 0.12) !important; color: #7367f0 !important; border: none">
                                        <p class="align-self-start">{{ createStatement["content_" + locale].substr(0, 48) +
                                            "..." }}</p>
                                        <span class="align-self-end p-2 justify-center" @click="onCreateRemove(createStatement.id)" style="cursor: pointer">x</span>
                                    </div>
                                </div>
                                <div class="invalid-feedback">{{ interviewCreateErrors.statements?.message }}</div>
                            </div>
                            <button type="button" class="btn btn-primary" @click="interviewCreate" :disabled="conductReady">{{ collection?.messages?.create }}</button>
                        </div>
                        <!-- Available Statements & Interview Details-->
                        <div class="col-6" style="flex: 0 0 50%; min-width: 50%">
                            <!-- Interview Details Card -->
                            <div class="card mb-2" v-if="existingActive.agenda != null">
                                <div class="card-header border-bottom">
                                    <h4 class="card-title">{{ collection?.messages?.interview }} {{
                                        collection?.messages?.details }}</h4>
                                </div>
                                <div class="card-body py-2 my-25">
                                    <!-- Interview Details-->
                                    <div class="row">
                                        <p>
                                            <strong>{{ collection?.messages?.agenda }}: </strong>{{ existingActive.agenda }}
                                        </p>
                                        <p>
                                            <strong>{{ collection?.messages?.interviewee }}: </strong>{{
                                                existingActive.interviewee }}
                                        </p>
                                        <p v-if="existingActive.scheduled_date">
                                            <strong>{{ collection?.messages?.scheduledDate }}: </strong>{{ formatScheduledDate(existingActive.scheduled_date) }}
                                        </p>
                                        <p v-if="existingActive.status">
                                            <strong>{{ collection?.messages?.status }}: </strong>
                                            <span :class="`badge bg-${getStatusColor(existingActive.status)}`">{{ getStatusText(existingActive.status) }}</span>
                                        </p>
                                    </div>

                                    <!-- Schedule Meeting Section -->
                                    <div class="schedule-meeting-section mt-2 mb-3">
                                        <div v-if="!showScheduleForm && !existingActive.scheduled_date" class="mb-2">
                                            <button class="btn btn-success" @click="showScheduleForm = true">
                                                <i class="feather icon-calendar"></i> {{ collection?.messages?.scheduleMeeting }}
                                            </button>
                                        </div>

                                        <!-- Schedule/Edit Form -->
                                        <div v-if="showScheduleForm || existingActive.scheduled_date" class="card bg-light-info mb-2">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ existingActive.scheduled_date ? collection?.messages?.meetingDetails : collection?.messages?.scheduleMeeting }}</h5>

                                                <div class="mb-1">
                                                    <label class="form-label" for="scheduledDateTime">{{ collection?.messages?.date }} & {{ collection?.messages?.time }}</label>
                                                    <input
                                                        type="datetime-local"
                                                        class="form-control"
                                                        id="scheduledDateTime"
                                                        v-model="scheduleForm.datetime"
                                                        :disabled="!scheduleForm.isEditing && existingActive.scheduled_date"
                                                    />
                                                </div>

                                                <div class="mb-1">
                                                    <label class="form-label" for="meetingDuration">{{ collection?.messages?.duration || 'Varaktighet' }} ({{ collection?.messages?.minutes }})</label>
                                                    <select
                                                        class="form-select"
                                                        id="meetingDuration"
                                                        v-model="scheduleForm.duration"
                                                        :disabled="!scheduleForm.isEditing && existingActive.scheduled_date"
                                                    >
                                                        <option value="15">15 {{ collection?.messages?.minutes }}</option>
                                                        <option value="30">30 {{ collection?.messages?.minutes }}</option>
                                                        <option value="45">45 {{ collection?.messages?.minutes }}</option>
                                                        <option value="60" selected>60 {{ collection?.messages?.minutes }}</option>
                                                        <option value="90">90 {{ collection?.messages?.minutes }}</option>
                                                        <option value="120">120 {{ collection?.messages?.minutes }}</option>
                                                    </select>
                                                </div>

                                                <div class="d-flex gap-1 mt-2">
                                                    <button
                                                        v-if="!existingActive.scheduled_date || scheduleForm.isEditing"
                                                        class="btn btn-primary me-1"
                                                        @click="scheduleMeeting"
                                                    >
                                                        <i class="feather icon-send"></i> {{ existingActive.scheduled_date ? collection?.messages?.update || 'Uppdatera' : collection?.messages?.send || 'Skicka' }}
                                                    </button>

                                                    <button
                                                        v-if="existingActive.scheduled_date && !scheduleForm.isEditing"
                                                        class="btn btn-warning me-1"
                                                        @click="enableEdit"
                                                    >
                                                        <i class="feather icon-edit"></i> {{ collection?.messages?.edit || 'Redigera' }}
                                                    </button>

                                                    <button
                                                        v-if="existingActive.scheduled_date && !scheduleForm.isEditing"
                                                        class="btn btn-danger"
                                                        @click="cancelMeeting"
                                                    >
                                                        <i class="feather icon-x"></i> {{ collection?.messages?.cancelMeeting }}
                                                    </button>

                                                    <button
                                                        v-if="showScheduleForm && !existingActive.scheduled_date"
                                                        class="btn btn-secondary"
                                                        @click="showScheduleForm = false"
                                                    >
                                                        {{ collection?.messages?.cancel }}
                                                    </button>

                                                    <button
                                                        v-if="scheduleForm.isEditing"
                                                        class="btn btn-secondary"
                                                        @click="cancelEdit"
                                                    >
                                                        {{ collection?.messages?.cancel }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Interview Statements-->
                                    <p>
                                        <strong>{{ collection?.messages?.statements }}</strong>
                                    </p>
                                    <div class="card mb-4">
                                        <ul class="list-group list-group-flush">
                                            <li v-for="statement in existingActive.statements" :key="statement" class="list-group-item d-flex align-items-between" style="display: flex; justify-content: space-between; align-items: center">
                                                <p>{{ statement["content_" + locale].substring(0, 48) + "..." }}</p>
                                                <button type="button" class="btn btn-danger remove-wishlist" @click="removeFromActive(statement.id)">
                                                    <span>{{ collection?.messages?.remove }}</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                    <button class="btn btn-primary mt-1 me-1" @click="interviewUpdate('update')">{{
                                        collection?.messages?.saveChanges }}</button>
                                    <button class="btn btn-outline-danger mt-1" @click="interviewUpdate('delete')">{{
                                        collection?.messages?.delete }} {{ collection?.messages?.interview }}</button>
                                </div>
                            </div>
                            <!-- Available Statements -->
                            <div class="card mt-2">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ collection?.messages?.availableStatements || 'Available Statements' }} ({{ interviewStatements.length }})</h5>
                                </div>
                                <div class="card-body p-2" style="max-height: 500px; overflow-y: auto;">
                                    <div v-if="interviewStatements.length === 0" class="text-center text-muted p-3">
                                        <i class="feather icon-check-circle" style="font-size: 2rem;"></i>
                                        <p class="mb-0">{{ collection?.messages?.noStatementsAvailable || 'No statements available for interviews' }}</p>
                                    </div>
                                    <div class="row accordion accordion-margin" id="interviewStatements">
                                        <div v-for="interviewStatement in interviewStatements" :key="interviewStatement.id" class="card accordion-item mb-2" :draggable="true" @dragstart="dragEmit(interviewStatement.id)" style="cursor: grab;">
                                            <div class="row px-2 pt-2">
                                                <div class="col-8">
                                                    <small class="text-muted"><i class="feather icon-move"></i> {{ collection?.messages?.dragToAdd || 'Drag to add' }}</small>
                                                </div>
                                                <div class="col-4 d-flex justify-content-end align-items-center">
                                                    <span v-if="interviewStatement.latestReview" :class="`badge rounded-pill badge-glow bg-${interviewStatement.latestReview?.class}`" style="height: 1.5rem">{{ interviewStatement.latestReview?.review_status }}</span>
                                                </div>
                                            </div>
                                            <h2 class="accordion-header" :id="`interviewStatementHeader${interviewStatement?.id}`">
                                                <button class="accordion-button collapsed" data-bs-toggle="collapse" role="button" :data-bs-target="`#interviewStatement${interviewStatement?.id}`" aria-expanded="false" :aria-controls="`interviewStatement${interviewStatement?.id}`">
                                                    {{ interviewStatement["content_" + locale] }}
                                                </button>
                                            </h2>

                                            <div :id="`interviewStatement${interviewStatement?.id}`" class="collapse accordion-collapse" :aria-labelledby="`interviewStatementHeader${interviewStatement?.id}`" data-bs-parent="interviewStatements">
                                                <div class="accordion-body">
                                                    {{ interviewStatement["desc_" + locale] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    name: "Interview",
    props: ["actionId", "collection", "locale", "org"],
    data() {
        return {
            conductReady: false,
            dragId: null,
            interviews: [],
            interviewActive: "prepare",
            interviewCreateErrors: {
                agenda: null,
                user: null,
                statements: null,
            },
            interviewExpanded: {
                id: null,
                agenda: null,
                statements: [],
            },
            interviewStatements: [],
            toCreate: [],
            interviewStatementsSelected: [],
            existingActive: {
                id: null,
                agenda: null,
                interviewee: null,
                statements: [],
                scheduled_date: null,
                status: null,
            },
            showScheduleForm: false,
            scheduleForm: {
                datetime: '',
                duration: 60,
                isEditing: false,
            },
        };
    },
    methods: {
        dragEmit(id) {
            this.dragId = id;
        },
        dragReceive() {
            var thisComponent = this;
            // remove from interviewStatements
            let modifiedStatements = this.interviewStatements.filter((s) => {
                return s.id != thisComponent.dragId;
            });
            let dragged = this.interviewStatements.filter((s) => {
                return s.id == thisComponent.dragId;
            });
            thisComponent.interviewStatements = modifiedStatements;
            thisComponent.toCreate.push(dragged[0]);
        },
        interviewClear() {
            this.interviewCreateErrors = {
                agenda: null,
                user: null,
                statements: null,
            };
            // empty state
            document.getElementById("interviewCreateUser").value = null;
            document.getElementById("agenda").value = null;
            this.interviewStatementsSelected = [];
            this.toCreate = [];
        },
        interviewCreate() {
            var thisComponent = this;
            Swal.fire({
                title: "Info!",
                text: `${thisComponent.collection?.messages?.working} ...`,
                icon: "info",
                showConfirmButton: false,
                customClass: {
                    confirmButton: "btn btn-primary",
                },
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                },
            });
            // clear all errors
            this.interviewCreateErrors = {
                agenda: null,
                user: null,
                statements: null,
            };
            let load = {
                agenda: null,
                user: null,
                statements: [],
                locale: thisComponent.locale,
                interviewee: null,
                organisation_id: null,
                plan_id: 1
            };
            // make sure there is an agenda
            load.agenda = document.getElementById("agenda").value;
            if (load.agenda == "" || load.agenda == null) {
                this.interviewCreateErrors.agenda = {
                    message: "Agenda is required",
                };
            }
            load.user = document.getElementById("interviewCreateUser").value;
            if (load.user == "" || load.user == null) {
                this.interviewCreateErrors.user = {
                    message: "Interviewee email is required",
                };
            } else {
                // Validate email format
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(load.user)) {
                    this.interviewCreateErrors.user = {
                        message: "Please enter a valid email address",
                    };
                }
            }
            let p = this.toCreate;
            if (p.length == 0) {
                this.interviewCreateErrors.statements = {
                    message: "At least one statement is required",
                };
            } else {
                p.forEach(function (s) {
                    load.statements.push(s.id);
                });
            }
            if (this.interviewCreateErrors.agenda || this.interviewCreateErrors.user || this.interviewCreateErrors.statements) {
                Swal.close();
                return;
            } else {
                load.interviewee = load.user;
                load.organisation_id = this.org;
                //console.log(load);
                //return;
                axios
                    .post(`/${this.locale}/interviews/`, load)
                    .then(function (response) {
                        Swal.close();
                        //thisComponent.interviewHide();
                        thisComponent.rebuild(true);
                        // Update parent to enable Conduct button
                        if (thisComponent.$parent && thisComponent.$parent.rebuild) {
                            thisComponent.$parent.rebuild();
                        }
                        thisComponent.$nextTick(() => {
                            thisComponent.interviewClear();
                            Swal.fire({
                                title: "Success!",
                                text: "Interview created! You can now schedule a meeting for this interview.",
                                icon: "success",
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                },
                                buttonsStyling: false,
                            });
                            return;
                        });
                        //console.log(response.data);
                    })
                    .catch(function (error) {
                        console.log(error.response);
                        Swal.close();
                        thisComponent.$nextTick(() => {
                            Swal.fire({
                                title: error,
                                text: error.response?.data?.message,
                                icon: "error",
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                },
                                buttonsStyling: false,
                            });
                            return;
                        });
                    });
            }
        },
        interviewCreateDropHandle(e) {
            console.log(e);
        },
        interviewHide() {
            var thisComponent = this;
            this.interviewActive = "prepare";
            // clear all errors
            this.interviewCreateErrors = {
                agenda: null,
                user: null,
                statements: null,
            };
            // empty state
            document.getElementById("interviewCreateUser").value = null;
            document.getElementById("agenda").value = null;
            this.toCreate = [];
            //document.getElementById("interviewStatementsSelect").options = [];
            this.interviewStatementsSelected = [];
            this.$nextTick(() => {
                $("#interviewModal").modal("hide");
                // rebuild parent
                thisComponent.$parent.rebuild();
            });
        },
        interviewUpdate(type) {
            //console.log(type);
            //console.log(this.existingActive);
            var thisComponent = this;
            let load;
            if (type == "update") {
                load = this.existingActive;
            }
            if (type == "delete") {
                load = this.existingActive;
                load.statements = [];
            }
            if (this.existingActive.id != null) {
                Swal.fire({
                    title: "Info!",
                    text: `${thisComponent.collection?.messages?.working} ...`,
                    icon: "info",
                    showConfirmButton: false,
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    buttonsStyling: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    },
                });
                console.log(load);
                axios
                    .post(`/${this.locale}/axios/interviews/${this.existingActive.id}/update`, load)
                    .then(function (response) {
                        Swal.close();
                        thisComponent.rebuild();
                        thisComponent.$nextTick(() => {
                            thisComponent.interviewClear();
                            Swal.fire({
                                title: "Success!",
                                text: "Operation Completed!",
                                icon: "success",
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                },
                                buttonsStyling: false,
                            });
                            return;
                        });

                        console.log(response.data);
                    })
                    .catch(function (error) {
                        thisComponent.rebuild();
                        console.log(error.response);
                        thisComponent.$nextTick(() => {
                            Swal.fire({
                                title: error,
                                text: error.response?.data?.message,
                                icon: "error",
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                },
                                buttonsStyling: false,
                            });
                            return;
                        });
                    });
            }
        },
        interviewStatementAdd(statement) {
            let exists = this.interviewStatementsSelected.filter((is) => {
                return is.id == statement.id;
            });
            if (exists.length == 0) {
                this.interviewStatementsSelected.push(statement);
            }
        },
        interviewStatementRemove(statement) {
            this.interviewStatementsSelected = this.interviewStatementsSelected.filter((is) => {
                return is.id != statement.id;
            });
        },
        interviewStatementRemovable(statement) {
            let added = this.interviewStatementsSelected.filter((is) => {
                return is.id == statement.id;
            });
            if (added.length == 0) {
                return false;
            } else {
                return true;
            }
        },
        onCreateRemove(id) {
            // remove from onCreate set
            let newOnCreate = this.toCreate.filter((c) => {
                return c.id != id;
            });
            let pending = this.toCreate.filter((c) => {
                return c.id == id;
            });
            pending = pending[0];
            this.toCreate = newOnCreate;
            // add to interviewStatements
            let is = this.interviewStatements;
            is.push(pending);
            is.sort((a, b) => a.id - b.id);
            this.interviewStatements = is;
        },
        existingSetActive(intId) {
            let int = this.interviews.filter((i) => {
                return i.id == intId;
            });
            console.log('Selected interview:', int);
            int = int[0];
            this.existingActive = {
                ...int,
                scheduled_date: int.scheduled_date || null,
                status: int.status || null,
            };
            this.showScheduleForm = false;
            this.scheduleForm.isEditing = false;
            this.scheduleForm.datetime = '';
            this.scheduleForm.duration = 60;
        },
        interviewPrepare() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/review/" + thisComponent.actionId, {})
                .then(function (response) {
                    //console.log(response.data);
                    thisComponent.interviewStatements = response.data.statistics?.statements?.interview?.statements;
                    if (thisComponent.interviewStatements.length == 0) {
                        thisComponent.conductReady = true;
                    } else {
                        thisComponent.conductReady = false;
                    }
                    if (response.data.statistics?.statements?.interview?.interviews.length > 0) {
                        thisComponent.interviews = response.data.statistics.statements.interview.interviews;
                        const firstInterview = response.data.statistics.statements.interview.interviews[0];
                        thisComponent.existingActive = {
                            ...firstInterview,
                            scheduled_date: firstInterview.scheduled_date || null,
                            status: firstInterview.status || null,
                        };
                    }
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
            /*
            console.log(this.collection);
            this.interviewStatements = this.collection.statistics?.statements?.interview?.statements;
            console.log(this.interviewStatements)
            */
            /*
            var thisComponent = this;
            // load interview related data
            axios
                .get(`/${this.locale}/axios/organisations/${this.org}/review/action/${this.actionId}/interview`)
                .then(function (response) {
                    //console.log(response.data);
                    thisComponent.interviewStatements = response.data;
                    thisComponent.$nextTick(() => {
                        thisComponent.interviewExpanded = thisComponent.interviewStatements.interviews[0];
                    });
                })
                .catch(function (error) {
                    console.log(error.response);
                });
                */
            $("#interviewModal").modal("show");
        },
        rebuild(selectNewest = false) {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/review/" + thisComponent.actionId, {})
                .then(function (response) {
                    const allInterviews = response.data.statistics.statements.interview.interviews;
                    thisComponent.interviews = allInterviews;
                    thisComponent.interviewStatements = response.data.statistics?.statements?.interview?.statements;
                    if (thisComponent.interviewStatements.length == 0) {
                        thisComponent.conductReady = true;
                    } else {
                        thisComponent.conductReady = false;
                    }
                    if (allInterviews.length > 0) {
                        thisComponent.$nextTick(() => {
                            let selectedInterview;

                            if (selectNewest) {
                                selectedInterview = allInterviews[allInterviews.length - 1];
                            } else if (thisComponent.existingActive.id) {
                                selectedInterview = allInterviews.find(i => i.id === thisComponent.existingActive.id) || allInterviews[0];
                            } else {
                                selectedInterview = allInterviews[0];
                            }

                            thisComponent.existingActive = {
                                ...selectedInterview,
                                scheduled_date: selectedInterview.scheduled_date || null,
                                status: selectedInterview.status || null,
                            };
                            thisComponent.$forceUpdate();
                        });
                    } else {
                        thisComponent.interviews = [];
                        thisComponent.existingActive = {
                            id: null,
                            agenda: null,
                            interviewee: null,
                            statements: [],
                            scheduled_date: null,
                            status: null,
                        };
                    }
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
        },
        removeFromActive(id) {
            this.existingActive.statements = this.existingActive.statements.filter((s) => {
                return s.id != id;
            });
        },
        formatScheduledDate(date) {
            if (!date) return '';
            const d = new Date(date);
            return d.toLocaleString(this.locale === 'se' ? 'sv-SE' : 'en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        getStatusColor(status) {
            const colors = {
                'planned': 'info',
                'in_progress': 'warning',
                'completed': 'success',
                'cancelled': 'danger'
            };
            return colors[status] || 'secondary';
        },
        getStatusText(status) {
            const texts = {
                'planned': this.collection?.messages?.planned || 'Planerad',
                'in_progress': this.collection?.messages?.inProgress || 'Pågående',
                'completed': this.collection?.messages?.completed || 'Genomförd',
                'cancelled': this.collection?.messages?.cancelled || 'Avbokad'
            };
            return texts[status] || status;
        },
        enableEdit() {
            this.scheduleForm.isEditing = true;
            if (this.existingActive.scheduled_date) {
                const date = new Date(this.existingActive.scheduled_date);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                this.scheduleForm.datetime = `${year}-${month}-${day}T${hours}:${minutes}`;
            }
        },
        cancelEdit() {
            this.scheduleForm.isEditing = false;
            this.scheduleForm.datetime = '';
            this.scheduleForm.duration = 60;
        },
        scheduleMeeting() {
            const thisComponent = this;

            if (!this.scheduleForm.datetime) {
                Swal.fire({
                    title: 'Error!',
                    text: this.collection?.messages?.pleaseSelectDateTime || 'Vänligen välj datum och tid',
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    buttonsStyling: false,
                });
                return;
            }

            const isUpdate = this.existingActive.scheduled_date && this.scheduleForm.isEditing;
            const method = isUpdate ? 'put' : 'post';
            const url = `/${this.locale}/interviews/${this.existingActive.id}/schedule`;

            Swal.fire({
                title: 'Info!',
                text: `${this.collection?.messages?.working} ...`,
                icon: 'info',
                showConfirmButton: false,
                customClass: {
                    confirmButton: 'btn btn-primary',
                },
                buttonsStyling: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            axios({
                method: method,
                url: url,
                data: {
                    scheduled_date: this.scheduleForm.datetime,
                    duration: this.scheduleForm.duration
                }
            })
            .then(function (response) {
                Swal.close();
                thisComponent.showScheduleForm = false;
                thisComponent.scheduleForm.isEditing = false;
                thisComponent.scheduleForm.datetime = '';
                thisComponent.rebuild(false);

                Swal.fire({
                    title: 'Success!',
                    text: response.data.message || (isUpdate ? thisComponent.collection?.messages?.meetingUpdated : thisComponent.collection?.messages?.meetingInvitationSent),
                    icon: 'success',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    buttonsStyling: false,
                });
            })
            .catch(function (error) {
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: error.response?.data?.message || 'Failed to schedule meeting',
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                    },
                    buttonsStyling: false,
                });
            });
        },
        cancelMeeting() {
            const thisComponent = this;

            Swal.fire({
                title: this.collection?.messages?.confirmCancelMeeting || 'Avboka möte?',
                text: this.collection?.messages?.confirmCancelMeetingText || 'Är du säker på att du vill avboka detta möte?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: this.collection?.messages?.yesCancel || 'Ja, avboka',
                cancelButtonText: this.collection?.messages?.cancel || 'Avbryt',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Info!',
                        text: `${thisComponent.collection?.messages?.working} ...`,
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });

                    axios.delete(`/${thisComponent.locale}/interviews/${thisComponent.existingActive.id}/schedule`)
                    .then(function (response) {
                        Swal.close();
                        thisComponent.showScheduleForm = false;
                        thisComponent.scheduleForm.isEditing = false;
                        thisComponent.scheduleForm.datetime = '';
                        thisComponent.rebuild(false);

                        Swal.fire({
                            title: 'Success!',
                            text: response.data.message || thisComponent.collection?.messages?.meetingCancelled,
                            icon: 'success',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                            },
                            buttonsStyling: false,
                        });
                    })
                    .catch(function (error) {
                        Swal.close();
                        Swal.fire({
                            title: 'Error!',
                            text: error.response?.data?.message || 'Failed to cancel meeting',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                            },
                            buttonsStyling: false,
                        });
                    });
                }
            });
        },
    },
    mounted() {
        this.interviews = this.collection?.statistics.statements.interview.interviews;
    }
};
</script>
<style>
.scrollable-container {
    height: 300px;
    /* Set the desired scrollable height */
    border: 1px solid #ccc;
    overflow: auto;
}
</style>
