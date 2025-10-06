<template>
    <div class="modal fade col-12 full-width" id="interviewModal" tabindex="-1" aria-labelledby="interviewModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ collection?.messages?.interview }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="interviewHide"></button>
                </div>
                <div class="modal-body">
                    <p>{{ collection?.messages.interview }}</p>
                    <div class="col-12 mb-1 mb-lg-0 text-center">
                        <!-- button group checkbox -->
                        <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                            <input type="checkbox" class="btn-check" id="btncheck1" :checked="interviewActive == 'prepare' ? true : false" autocomplete="off" @click="interviewActive = 'prepare'" />
                            <label class="btn btn-primary" for="btncheck1">{{ collection?.messages?.interviewPrepare }}</label>
                            <input type="checkbox" class="btn-check" id="btncheck2" :checked="interviewActive == 'conduct' ? true : false" autocomplete="off" @click="interviewActiveSet('conduct')" disabled />
                            <label class="btn btn-primary" for="btncheck2">{{ collection?.messages?.interviewConduct }}</label>
                        </div>
                    </div>
                </div>
                <!--<div class="modal-footer justify-content-start">-->
                <div class="px-2 py-1">
                    <div class="row" v-if="interviewActive == 'prepare'">
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
                                    <!-- Content that can be dragged into the container -->
                                </div>
                                <div class="invalid-feedback">{{ interviewCreateErrors.statements?.message }}</div>
                            </div>
                            <!--
                            <div class="mb-1" id="statementsContainer">
                                
                                <label class="form-label" for="interviewStatementsSelect">{{ collection?.messages?.statements }}</label>
                                <select :class="`form-select ${interviewCreateErrors.statements ? 'is-invalid' : ''}`" id="interviewStatementsSelect" multiple="multiple" disabled style="height: 250px">
                                    <option v-for="interviewStatementSelected in interviewStatementsSelected" :key="interviewStatementSelected" :value="interviewStatementSelected.id">
                                        {{ interviewStatementSelected["content_" + locale].substr(0, 36) }}
                                    </option>
                                </select>
                                <div class="invalid-feedback">{{ interviewCreateErrors.statements?.message }}</div>
                            
                        </div>
                    -->
                            <button type="button" class="btn btn-primary" @click="interviewCreate">{{ collection?.messages?.create }}</button>
                        </div>
                        <!-- Draggable Statements-->
                        <div class="col-6 scrollable-container" style="flex: 0 0 50%; min-width: 50%">
                            <!-- Existing Interviews-->
                            <div class="row scrollable-container mb-1">
                                <div class="col-4">
                                    <div class="faq-navigation d-flex justify-content-between flex-column mb-2 mb-md-0">
                                        <!-- pill tabs navigation -->
                                        <ul class="nav nav-pills nav-left flex-column" role="tablist">
                                            <!-- payment -->
                                            <li v-for="int in collection?.statistics?.statements?.interview?.interviews" :key="int" class="nav-item">
                                                <a :class="`nav-link ${existingActive.id == int.id ? 'active' : ''}`" aria-expanded="true" :id="`existingInterview${int.id}`" role="tab" @click="existingSetActive(int.id)">
                                                    <span class="fw-bold">{{ collection?.messages?.interview + "-" + int.id }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <!-- pill tabs tab content -->
                                    <div class="tab-content">
                                        <!-- payment panel -->
                                        <div role="tabpanel" class="tab-pane active" id="faq-payment" aria-labelledby="payment" aria-expanded="true">
                                            <!-- icon and header -->
                                            <!-- frequent answer and question  collapse  -->
                                            <div v-for="eStatement in existingActive.statements" :key="eStatement" class="accordion accordion-margin mt-2">
                                                <div class="card accordion-item">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed" data-bs-toggle="collapse" role="button" data-bs-target="#faq-payment-one" aria-expanded="false" disabled>{{ eStatement["content_" + locale] }}</button>
                                                    </h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p>xxxxxxxx</p>
                            <div class="row accordion accordion-margin mt-2" id="interviewStatements">
                                <p>xxxxxxxxxxxxxxx</p>
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
                    <div class="row" v-if="interviewActive == 'conduct'">
                        <div class="col-6">
                            <!--- Interviews Table-->
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ collection?.messages?.tables }}</h4>
                                </div>
                                <div class="table">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>{{ collection?.messages?.creator }}</th>
                                                <th>{{ collection?.messages?.interviewee }}</th>
                                                <th class="text-center">{{ collection?.messages?.actions }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="inter in interviewStatements.interviews" :key="inter">
                                                <td>
                                                    <span class="fw-bold">{{ inter.id }}</span>
                                                </td>
                                                <td>
                                                    {{ inter.creator }}
                                                </td>
                                                <td>
                                                    {{ inter.interviewee }}
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" :class="`btn btn-relief-${inter.id == interviewExpanded.id ? 'warning' : 'dark'}`" @click="interviewConductedSet(inter.id)">
                                                        {{ inter.id == interviewExpanded.id ? collection?.messages?.expanded : collection?.messages?.expand }}
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <!-- Agenda Display -->
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">{{ collection?.messages?.agenda }}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="border p-3 rounded">
                                        {{ interviewExpanded?.agenda }}
                                    </div>
                                </div>
                            </div>
                            <!-- Accordion for Interview Statements -->
                            <div class="accordion" :id="`interviewStatementsAccordion${interviewExpanded.id}`">
                                <div v-for="statement in interviewExpanded.statements" :key="statement" class="card accordion-item">
                                    <h2 class="accordion-header" :id="`interviewStatementHeader${statement.id}`">
                                        <button class="accordion-button collapsed" data-bs-toggle="collapse" role="button" :data-bs-target="`#interviewStatement${statement.id}`" aria-expanded="false" :aria-controls="`interviewStatement${statement.id}`">
                                            {{ statement["content_" + locale] }}
                                        </button>
                                    </h2>
                                    <div :id="`interviewStatement${statement.id}`" class="collapse accordion-collapse" :aria-labelledby="`interviewStatementHeader${statement.id}`" :data-bs-parent="`#interviewStatementsAccordion${interviewExpanded.id}`">
                                        <div class="accordion-body">
                                            {{ statement["desc_" + locale] }}
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="text-uppercase">{{ collection?.messages?.value }}</h4>
                                            </div>
                                            <div class="card-body">
                                                <div :id="`interviewStatementValueSlider${interviewExpanded.id}_${statement.id}`" class="mt-1 mb-3"></div>
                                                <div class="mb-3">
                                                    <label for="interviewReviewText{{ interviewExpanded.id }}_{{ statement.id }}" class="form-label">{{ collection?.messages?.review }}</label>
                                                    <textarea class="form-control" :id="`interviewReviewText${interviewExpanded.id}_${statement.id}`" :name="`interviewReviewText${interviewExpanded.id}_${statement.id}`" rows="4"></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <button type="button" class="btn btn-success w-25 me-2" @click="reviewUpdate('accept', statement.id, `interviewReviewText${interviewExpanded.id}_${statement.id}`, `interviewStatementValueSlider${interviewExpanded.id}_${statement.id}`, statement.latestDeed?.id)">
                                                        {{ collection?.messages?.accept }}
                                                    </button>
                                                    <button type="button" class="btn btn-danger w-25" @click="reviewUpdate('reject', statement.id, `interviewReviewText${interviewExpanded.id}_${statement.id}`, `interviewStatementValueSlider${interviewExpanded.id}_${statement.id}`, statement.latestDeed?.id)">
                                                        {{ collection?.messages?.reject }}
                                                    </button>
                                                </div>
                                                <p>{{ collection?.messages?.lastUpdated }}: {{ statement.latestDeed?.lastUpdated }}, {{ collection?.messages?.by }}: {{ statement.latestDeed?.user }}</p>
                                                <p>{{ collection?.messages?.comment }}: {{ statement.latestDeed?.comment }}</p>
                                                <p>{{ collection?.messages?.latestReview }}, {{ statement.latestReview?.user }}, {{ statement.latestReview?.lastUpdated }}: {{ statement.latestReview?.review }}</p>
                                                <p :class="`text-${statement.latestReview?.class}`">{{ statement.latestReview?.review_status }}</p>
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
            dragId: null,
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
        interviewActiveSet(t) {
            var thisComponent = this;
            this.interviewActive = t;
            this.$nextTick(() => {
                thisComponent.$nextTick(() => {
                    // create sliders for mounted interview statements
                    let slider = null;
                    thisComponent.interviewExpanded.statements.forEach((s) => {
                        slider = document.getElementById(`interviewStatementValueSlider${thisComponent.interviewExpanded.id}_${s.id}`);
                        console.log(slider);
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
            });
        },
        interviewConductedSet(interviewId) {
            var thisComponent = this;
            // axios call to refresh
            axios
                .get(`/${this.locale}/axios/organisations/${this.org}/review/action/${this.actionId}/interview`)
                .then(function (response) {
                    console.log(response.data);
                    thisComponent.interviewStatements = response.data;
                    thisComponent.$nextTick(() => {
                        let theInter = thisComponent.interviewStatements.interviews.filter((i) => {
                            return i.id == interviewId;
                        });
                        thisComponent.interviewExpanded = theInter[0];
                        // build sliders again
                        thisComponent.slidersRebuild();
                    });
                })
                .catch(function (error) {
                    console.log(error.response);
                });
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
                        thisComponent.interviewHide();
                        thisComponent.$nextTick(() => {
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
                    if (response.data.statistics?.statements?.interview?.interviews.length > 0) {
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
