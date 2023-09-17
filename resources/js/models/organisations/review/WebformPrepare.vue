<template>
    <div class="modal fade col-12 full-width" id="webformPrepareModal" tabindex="-1" aria-labelledby="webformPrepareModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ collection?.messages?.webform }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="webformPrepareHide"></button>
                </div>
                <div class="modal-body">
                    <div v-if="conductReady" class="avatar bg-light-success rounded full-width mb-1">
                        <p class="text-center full-width mt-0 mb-0 py-2">{{ collection?.messages?.readyToConduct }}</p>
                    </div>
                    <p>{{ collection?.messages.webform }}</p>
                </div>
                <div class="px-2 py-1">
                    <div class="row">
                        <!-- User Selection-->
                        <div class="col-6" style="flex: 0 0 50%; min-width: 50%">
                            <div class="mb-1">
                                <label class="form-label" for="webformUserSelect">{{ collection?.messages?.interviewee }}</label>
                                <select class="form-select" id="webformUserSelect">
                                    <option v-for="user in users" :key="user" :value="user.id">{{ user.name }} [{{ user.role }}]</option>
                                </select>
                            </div>
                            <div class="scrollable-container mb-1" @dragover.prevent @drop="dragReceive">
                                <div :class="`inner-content`">
                                    <div v-for="addedStatement in addedStatements" :key="addedStatement" class="d-flex justify-content-between align-items-center" style="background: rgba(115, 103, 240, 0.12) !important; color: #7367f0 !important; border: none">
                                        <p class="align-self-start">{{ addedStatement["content_" + locale].substr(0, 48) + "..." }}</p>
                                        <span class="align-self-end p-2 justify-center" @click="addedStatementRemove(addedStatement.id)" style="cursor: pointer">x</span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" @click="interviewCreate" :disabled="conductReady">{{ collection?.messages?.create }}</button>
                        </div>
                        <!-- Available Statements -->
                        <div class="col-6" style="flex: 0 0 50%; min-width: 50%">
                            <div class="row accordion accordion-margin mt-2" id="webformStatements">
                                <div v-for="availableStatement in availableStatements" :key="availableStatement" class="card accordion-item" :draggable="true" @dragstart="dragEmit(availableStatement.id)">
                                    <div class="row">
                                        <div class="col-8"></div>
                                        <div class="col-4 d-flex justify-content-end align-items-center">
                                            <span :class="`badge rounded-pill badge-glow bg-${availableStatement.latestReview?.class}`" style="height: 1.5rem">{{ availableStatement.latestReview?.review_status }}</span>
                                        </div>
                                    </div>
                                    <h2 class="accordion-header" :id="`webformStatementHeader${availableStatement?.id}`">
                                        <button class="accordion-button collapsed" data-bs-toggle="collapse" role="button" :data-bs-target="`#webformStatement${availableStatement?.id}`" aria-expanded="false" :aria-controls="`webformStatement${availableStatement?.id}`">
                                            {{ availableStatement["content_" + locale] }}
                                        </button>
                                    </h2>

                                    <div :id="`webformStatement${availableStatement?.id}`" class="collapse accordion-collapse" :aria-labelledby="`webformStatementHeader${availableStatement?.id}`" data-bs-parent="webformStatements">
                                        <div class="accordion-body">
                                            {{ availableStatement["desc_" + locale] }}
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
    name: "WebformPrepare",
    props: ["actionId", "collection", "locale", "org"],
    data() {
        return {
            availableStatements: [],
            addedStatements: [],
            conductReady: false,
            dragId: null,
            users: [],
        }
    },
    methods: {
        addedStatementRemove(id) {
            // remove an added statement from the box
            let newAddedStatements = this.addedStatements.filter((s) => {
                return s.id != id;
            });
            let pending = this.addedStatements.filter((s) => {
                return s.id == id;
            });
            pending = pending[0];
            this.addedStatements = newAddedStatements;
            // add to available
            let av = this.availableStatements;
            av.push(pending);
            av.sort((a, b) => a.id - b.id);
            this.availableStatements = av;
        },
        dragEmit(id) {
            this.dragId = id;
        },
        dragReceive() {
            // on dragging a statement
            var thisComponent = this;
            // remove from interviewStatements
            let modifiedStatements = this.availableStatements.filter((s) => {
                return s.id != thisComponent.dragId;
            });
            let dragged = this.availableStatements.filter((s) => {
                return s.id == thisComponent.dragId;
            });
            thisComponent.availableStatements = modifiedStatements;
            thisComponent.addedStatements.push(dragged[0]);
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
            let load = {
                agenda: 'webform',
                user: null,
                statements: [],
                locale: thisComponent.locale,
                interviewee: null,
                organisation_id: null,
                plan_id: 3
            };
            // user
            load.interviewee = document.getElementById('webformUserSelect').value;
            // statements
            this.addedStatements.forEach(s => {
                load.statements.push(s.id);
            });
            //org
            load.organisation_id = this.org;
            axios
                .post(`/${this.locale}/interviews/`, load)
                .then(function (response) {
                    console.log(response);
                    Swal.close();
                    thisComponent.rebuild();
                    thisComponent.$nextTick(() => {
                        Swal.fire({
                            title: "Success!",
                            text: "Webform created!",
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
                            text: `${(error.response?.data?.message ? error.response.data.message : '')+(error.response?.data)}`,
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                            buttonsStyling: false,
                        });
                        return;
                    });
                });
        },
        rebuild() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/review/" + thisComponent.actionId, {})
                .then(function (response) {
                    thisComponent.users = response.data.statistics?.users;
                    thisComponent.availableStatements = response.data.statistics?.statements?.webform?.statements;
                    if (thisComponent.availableStatements.length == 0) {
                        thisComponent.conductReady = true;
                    } else {
                        thisComponent.conductReady = false;
                    }
                    // clear values
                    thisComponent.addedStatements = [];
                    thisComponent.dragId = null;
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
        },
        webformPrepare() {
            var thisComponent = this;
            axios
                .get("/" + thisComponent.locale + "/axios/organisations/review/" + thisComponent.actionId, {})
                .then(function (response) {
                   // console.log(response.data);
                    thisComponent.users = response.data.statistics?.users;
                    thisComponent.availableStatements = response.data.statistics?.statements?.webform?.statements;
                    //$('.select2').select2();
                    /*
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
                    */
                })
                .catch(function (error) {
                    console.log(error);
                    console.log(error.response);
                });
            $("#webformPrepareModal").modal("show");
        },
        webformPrepareHide() {

        }
    }
}
</script>