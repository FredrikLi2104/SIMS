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
                        <p class="text-center full-width mt-0 mb-0 py-2">Ready to Conduct</p>
                    </div>
                    <p>{{ collection?.messages.interview }}</p>
                </div>
                <div class="px-2 py-1">
                    <div class="row">
                        <!-- Input Details-->
                        <div class="col-6" style="flex: 0 0 50%; min-width: 50%">
                            <div class="mb-1">
                                <label class="form-label" for="user">
                                    {{ collection?.messages?.interviewee }}
                                </label>
                                <input :class="`form-control ${interviewCreateErrors.user ? 'is-invalid' : ''}`" id="interviewCreateUser" placeholder="John Smith" />
                                <div class="invalid-feedback">{{ interviewCreateErrors.user?.message }}</div>
                            </div>
                            <div class="mb-1">
                                <label class="form-label" for="agenda">{{ collection?.messages?.agenda }}</label>
                                <textarea :class="`form-control ${interviewCreateErrors.agenda ? 'is-invalid' : ''}`" id="agenda" rows="3" placeholder="Interview Agenda..."></textarea>
                                <div class="invalid-feedback">{{ interviewCreateErrors.agenda?.message }}</div>
                            </div>
                            <div class="scrollable-container mb-1" @dragover.prevent @drop="dragReceive">
                                <div :class="`inner-content ${interviewCreateErrors.statements ? 'is-invalid' : ''}`">
                                    <div v-for="createStatement in toCreate" :key="createStatement" class="d-flex justify-content-between align-items-center" style="background: rgba(115, 103, 240, 0.12) !important; color: #7367f0 !important; border: none">
                                        <p class="align-self-start">{{ createStatement["content_" + locale].substr(0, 48) + "..." }}</p>
                                        <span class="align-self-end p-2 justify-center" @click="onCreateRemove(createStatement.id)" style="cursor: pointer">x</span>
                                    </div>
                                </div>
                                <div class="invalid-feedback">{{ interviewCreateErrors.statements?.message }}</div>
                            </div>
                            <button type="button" class="btn btn-primary" @click="interviewCreate" :disabled="conductReady">{{ collection?.messages?.create }}</button>
                        </div>
                        <!-- Available Statements-->
                        <div class="col-6" style="flex: 0 0 50%; min-width: 50%">
                            <div class="row accordion accordion-margin mt-2" id="interviewStatements">
                                <div v-for="interviewStatement in interviewStatements" :key="interviewStatement" class="card accordion-item" :draggable="true" @dragstart="dragEmit(interviewStatement.id)">
                                    <div class="row">
                                        <div class="col-8"></div>
                                        <div class="col-4 d-flex justify-content-end align-items-center">
                                            <span :class="`badge rounded-pill badge-glow bg-${interviewStatement.class}`" style="height: 1.5rem">{{ interviewStatement.reviewStatus }}</span>
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
                    <!-- Existing Interviews-->
                    <div class="row mt-2">
                        <div class="modal-body">
                            <p>{{ collection?.messages.interviews }}</p>
                        </div>
                        <!-- Interview Pills-->
                        <div class="col-6">
                            <ul class="nav nav-pills mb-2">
                                <li class="nav-item" v-for="interview in interviews" :key="interview" @click="existingSetActive(interview.id)">
                                    <a :class="`nav-link ${existingActive.id == interview.id ? 'active' : ''}`">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        <span class="fw-bold">{{ collection?.messages?.interview }} {{ interview.id }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- Interview Card-->
                        <div class="col-6" v-if="existingActive.agenda != null">
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h4 class="card-title">{{ collection?.messages?.interview }} {{ collection?.messages?.details }}</h4>
                                </div>
                                <div class="card-body py-2 my-25">
                                    <!-- Interview Details-->
                                    <div class="row">
                                        <p>
                                            <strong>{{ collection?.messages?.agenda }}: </strong>{{ existingActive.agenda }}
                                        </p>
                                        <p>
                                            <strong>{{ collection?.messages?.interviewee }}: </strong>{{ existingActive.interviewee }}
                                        </p>
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
                                    <button class="btn btn-primary mt-1 me-1" @click="interviewUpdate('update')">{{ collection?.messages?.saveChanges }}</button>
                                    <button class="btn btn-outline-danger mt-1" @click="interviewUpdate('delete')">{{ collection?.messages?.delete }} {{ collection?.messages?.interview }}</button>
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
                    message: "Interviewee is required",
                };
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
                        thisComponent.rebuild();
                        thisComponent.$nextTick(() => {
                            thisComponent.interviewClear();
                            Swal.fire({
                                title: "Success!",
                                text: "Interview created!",
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
            });
        },
        interviewUpdate(type) {
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
            let int = this.collection?.statistics.statements.interview.interviews.filter((i) => {
                return i.id == intId;
            });
            int = int[0];
            this.existingActive = int;
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
                        thisComponent.existingActive = response.data.statistics.statements.interview.interviews[0];
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
        rebuild() {
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
                        //console.log(thisComponent.existingActive);
                        thisComponent.$nextTick(() => {
                            thisComponent.existingActive = response.data.statistics.statements.interview.interviews[0];
                            thisComponent.$forceUpdate();
                            thisComponent.interviews = response.data.statistics.statements.interview.interviews;
                        });
                    } else {
                        thisComponent.interviews = [];
                        thisComponent.existingActive = {
                            id: null,
                            agenda: null,
                            interviewee: null,
                            statements: [],
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
    },
};
</script>
<style>
.scrollable-container {
    height: 300px; /* Set the desired scrollable height */
    border: 1px solid #ccc;
    overflow: auto;
}
</style>
